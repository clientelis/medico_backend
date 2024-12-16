<?php

namespace App\Http\Controllers;

use App\Models\Medicament;
use Illuminate\Http\Request;
use App\Imports\MedicamentImport;
use Maatwebsite\Excel\Facades\Excel;

class MedicamentController extends Controller
{
    // Liste des médicaments avec recherche, pagination et filtres
    public function index(Request $request)
    {
        $query = Medicament::query();

        // Recherche par mot-clé (nom du produit ou DCI)
        if ($request->has('search') && !empty($request->search)) {
            $query->where('nom_produit', 'like', '%' . $request->search . '%')
                ->orWhere('dci', 'like', '%' . $request->search . '%');
        }

        // Filtrer par voie d'administration
        if ($request->has('voie') && !empty($request->voie)) {
            $query->where('voie_administration', $request->voie);
        }

        // Filtrer par type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        // Filtrer par forme galénique
        if ($request->has('forme') && !empty($request->forme)) {
            $query->where('forme', $request->forme);
        }

        // Filtrer par laboratoire
        if ($request->has('laboratoire') && !empty($request->laboratoire)) {
            $query->where('laboratoire', $request->laboratoire);
        }

        // Filtrer par pays d'origine
        if ($request->has('pays') && !empty($request->pays)) {
            $query->where('pays', $request->pays);
        }

        // Pagination
        $medicaments = $query->paginate($request->get('per_page', 15));

        return response()->json($medicaments);
    }

    // Récupérer un médicament par son ID
    public function show($id)
    {
        $medicament = Medicament::find($id);

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        return response()->json($medicament);
    }

    // Créer un nouveau médicament
    public function store(Request $request)
    {
        $request->validate([
            'laboratoire' => 'required|string|max:255',
            'nom_produit' => 'required|string|max:255',
            'conditionnement' => 'nullable|string',
            'prix_public' => 'nullable|numeric',
            'pays' => 'nullable|string|max:255',
            'voie_administration' => 'nullable|string|max:255',
            'forme' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'atc' => 'nullable|string|max:255',
            'dci' => 'nullable|string|max:255',
            'definition' => 'nullable|string',
            'conditionnement_detail' => 'nullable|string',
            'excipients' => 'nullable|string',
            'expiration_amm' => 'nullable|date',
            'numero_amm' => 'nullable|string|max:255',
        ]);

        $medicament = Medicament::create($request->all());

        return response()->json(['message' => 'Médicament créé avec succès', 'data' => $medicament], 201);
    }

    // Mettre à jour un médicament
    public function update(Request $request, $id)
    {
        $medicament = Medicament::find($id);

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        $request->validate([
            'laboratoire' => 'nullable|string|max:255',
            'nom_produit' => 'nullable|string|max:255',
            'conditionnement' => 'nullable|string',
            'prix_public' => 'nullable|numeric',
            'pays' => 'nullable|string|max:255',
            'voie_administration' => 'nullable|string|max:255',
            'forme' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'genre' => 'nullable|string|max:255',
            'atc' => 'nullable|string|max:255',
            'dci' => 'nullable|string|max:255',
            'definition' => 'nullable|string',
            'conditionnement_detail' => 'nullable|string',
            'excipients' => 'nullable|string',
            'expiration_amm' => 'nullable|date',
            'numero_amm' => 'nullable|string|max:255',
        ]);

        $medicament->update($request->all());

        return response()->json(['message' => 'Médicament mis à jour avec succès', 'data' => $medicament]);
    }

    // Supprimer un médicament
    public function destroy($id)
    {
        $medicament = Medicament::find($id);

        if (!$medicament) {
            return response()->json(['message' => 'Médicament non trouvé'], 404);
        }

        $medicament->delete();

        return response()->json(['message' => 'Médicament supprimé avec succès']);
    }

    // Importer un fichier CSV pour remplir la table medicaments
    public function importCsv(Request $request)
    {
        // Validation du fichier
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Vérifie que le fichier est bien un CSV
        ]);

        try {
            // Importation du fichier
            Excel::import(new MedicamentImport, $request->file('file'));

            return response()->json(['message' => 'Fichier importé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'importation : ' . $e->getMessage()], 500);
        }
    }
}