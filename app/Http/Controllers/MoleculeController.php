<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Molecule;
use Illuminate\Http\Request;

class MoleculeController extends Controller
{
    public function searchMolecule(Request $request)
    {
        $searchTerm = $request->input('libelle');

        if (is_null($searchTerm)) {
            return response()->json([
                'data' => [],
                'message' => 'No search term provided',
                'the request' => $request->input('libelle'),

                'status' => 200,
            ]);
        }
        $corresponding_molecules = Molecule::where(function ($query) use ($searchTerm) {
            $query->where('libelle', 'like', '%' . $searchTerm . '%')
                ->orWhere('formule', 'like', '%' . $searchTerm . '%');
        })->get();

        if ($corresponding_molecules->count() == 0) {
            return response()->json([
                'data' => [],
                'message' => 'No molecule found',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'data' => $corresponding_molecules,
            'message' => 'Molecules found !',
            'the request' => $request->input('libelle'),
            'status' => 200,

        ]);
    }

    public function checkInteractions(Request $request)
    {

        $moleculeIds = $request->input('molecule_ids');

        if (is_null($moleculeIds) || !is_array($moleculeIds)) {
            return response()->json([
                'data' => [],
                'message' => 'No molecule IDs provided',
                'status' => 400,
            ], 400);
        }
        $interactions = [];
        for ($i = 0; $i < count($moleculeIds); $i++) {

            for ($j = $i + 1; $j < count($moleculeIds); $j++) {
                $interaction = Interaction::where(function ($query) use ($moleculeIds, $i, $j) {
                    $query->where('moleculeA_id', $moleculeIds[$i])
                        ->where('moleculeB_id', $moleculeIds[$j]);
                })->orWhere(function ($query) use ($moleculeIds, $i, $j) {
                    $query->where('moleculeA_id', $moleculeIds[$j])
                        ->where('moleculeB_id', $moleculeIds[$i]);
                })->first();
                if ($interaction) {
                    
                    $interactions[] = [
                        'moleculeA' => Molecule::where('id', $moleculeIds[$i])->first(),
                        'moleculeB' => Molecule::where('id', $moleculeIds[$j])->first(),
                        'interaction' => $interaction
                    ];
                }
            }
        }

        return response()->json([
            'data' => $interactions,
            'message' => 'Interactions found',
            'status' => 200,
        ]);
    }
}
