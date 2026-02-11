<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;

class BoxController extends Controller
{
    // Return all boxes as JSON
    public function index()
    {
        $boxes = Box::all(['id', 'code', 'latitude', 'longitude', 'status', 'nameOfConsumer', 'numberOfConsumer']);

        return response()->json($boxes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:boxes',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nameOfConsumer' => 'nullable|string|max:255',
            'numberOfConsumer' => 'nullable|string|max:255',
            'status' => 'required|in:read,not_read,season',
        ]);

        $box = Box::create($validated);

        return response()->json($box, 201);
    }

    public function update(Request $request, $id)
    {
        $box = Box::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:boxes,code,' . $id,
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'nameOfConsumer' => 'nullable|string|max:255',
            'numberOfConsumer' => 'nullable|string|max:255',
            'status' => 'required|in:read,not_read,season',
        ]);

        $box->update($validated);

        return response()->json($box);
    }

    public function destroy($id)
    {
        $box = Box::findOrFail($id);
        $box->delete();

        return response()->json(null, 204);
    }

    public function showImportForm()
    {
        return view('import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'ljuca_file' => 'required|file|mimetypes:text/plain',
            'selim_file' => 'required|file|mimetypes:text/plain',
            'consumers_file' => 'required|file|mimes:xls,xlsx',
        ]);

        // Helper to parse a waypoint file
        $parseWaypoints = function ($filePath) {
            $map = [];
            $content = file_get_contents($filePath);
            $pattern = '/Waypoint\s+(\d+).*?([NS])(\d+)\s+([\d.]+)\s+([EW])(\d+)\s+([\d.]+)/';

            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $wpNumber = (int) $match[1];

                    $lat = floatval($match[3]) + (floatval($match[4]) / 60);
                    if (in_array($match[2], ['S', 'W'])) {
                        $lat = -$lat;
                    }

                    $lon = floatval($match[6]) + (floatval($match[7]) / 60);
                    if (in_array($match[5], ['S', 'W'])) {
                        $lon = -$lon;
                    }

                    $map[$wpNumber] = ['lat' => $lat, 'lon' => $lon];
                }
            }
            return $map;
        };

        // 1. Parse both waypoint files
        $ljucaMap = $parseWaypoints($request->file('ljuca_file')->getRealPath());
        $selimMap = $parseWaypoints($request->file('selim_file')->getRealPath());

        // 2. Read XLS
        $xlsFile = $request->file('consumers_file');

        try {
            $spreadsheet = IOFactory::load($xlsFile->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error reading Excel file: ' . $e->getMessage()]);
        }

        if (count($rows) < 3) {
            return back()->withErrors(['error' => 'Excel file is too short.']);
        }

        $headerRowIndex = 2;
        $header = $rows[$headerRowIndex];

        $colMap = [
            'code' => array_search('Čitački kod', $header),
            'coords' => array_search('Koordinate', $header),
            'consumer_name' => array_search('Naziv potrošača', $header),
            'consumer_number' => array_search('Potrošački broj', $header),
        ];

        if ($colMap['coords'] === false || $colMap['code'] === false) {
            return back()->withErrors(['error' => 'Required columns "Koordinate" or "Čitački kod" not found in row 3.']);
        }

        // 3. Insert Data
        $inserted = 0;
        $skipped = 0;
        $updated = 0;

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $coordRef = isset($row[$colMap['coords']]) ? trim((string) $row[$colMap['coords']]) : '';

            if (empty($coordRef)) {
                $skipped++;
                continue;
            }

            // Logic: 
            // - If Ends with 'S' -> use SELIM map (remove 'S')
            // - Else -> use LJUCA map

            $targetMap = null;
            $wpNumber = 0;

            if (str_ends_with($coordRef, 'S')) {
                // Remove 'S' and parse
                $numberPart = rtrim($coordRef, 'S');
                if (!ctype_digit($numberPart)) {
                    $skipped++;
                    continue;
                }
                $wpNumber = (int) $numberPart;
                $targetMap = $selimMap;
            } else {
                // Standard
                if (!ctype_digit($coordRef)) {
                    $skipped++;
                    continue;
                }
                $wpNumber = (int) $coordRef;
                $targetMap = $ljucaMap;
            }

            // Lookup
            if (!isset($targetMap[$wpNumber])) {
                $skipped++;
                continue;
            }

            $lat = $targetMap[$wpNumber]['lat'];
            $lon = $targetMap[$wpNumber]['lon'];

            $code = isset($row[$colMap['code']]) ? trim($row[$colMap['code']]) : null;
            $name = isset($row[$colMap['consumer_name']]) ? trim($row[$colMap['consumer_name']]) : null;
            $number = isset($row[$colMap['consumer_number']]) ? trim($row[$colMap['consumer_number']]) : null;

            if (!$code) {
                $skipped++;
                continue;
            }

            // Update or Create
            $box = Box::where('code', $code)->first();
            if ($box) {
                $box->update([
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'nameOfConsumer' => $name,
                    'numberOfConsumer' => $number,
                    'updated_at' => now(),
                ]);
                $updated++;
            } else {
                Box::create([
                    'code' => $code,
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'nameOfConsumer' => $name,
                    'numberOfConsumer' => $number,
                    'status' => 'not_read',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $inserted++;
            }
        }

        return redirect()->route('import.form')->with('success', "Import completed. Inserted: $inserted, Updated: $updated, Skipped: $skipped");
    }
}
