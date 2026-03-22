<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\MatierePremiere;
use App\Models\Produit;
use App\Models\Recette;
use App\Models\RecetteLigne;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Paramètres système ──────────────────────────────────
        \DB::table('parametres')->insert([
            ['cle' => 'jours_reference_paie',    'valeur' => '26',          'description' => 'Jours ouvrés de référence pour la paie'],
            ['cle' => 'seuil_ecart_caisse',       'valeur' => '5000',        'description' => 'Seuil alerte écart caisse (FCFA)'],
            ['cle' => 'devise',                   'valeur' => 'FCFA',        'description' => 'Devise d\'affichage'],
            ['cle' => 'nom_boulangerie',           'valeur' => 'DEGOGA',      'description' => 'Nom de la boulangerie'],
            ['cle' => 'adresse_boulangerie',       'valeur' => 'Abidjan, CI', 'description' => 'Adresse'],
            ['cle' => 'telephone_boulangerie',     'valeur' => '',            'description' => 'Téléphone'],
            ['cle' => 'taux_tva',                  'valeur' => '0',           'description' => 'Taux TVA (0 = exonéré)'],
        ]);

        // ── Utilisateur admin ───────────────────────────────────
        $admin = \App\Models\User::create([
            'name'      => 'Administrateur',
            'email'     => 'admin@boulangerie.ci',
            'telephone' => '0700000000',
            'password'  => Hash::make('password'),
        ]);

        // Rôles Spatie
        $roles = ['admin', 'responsable', 'caissier', 'boulanger', 'lecteur'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        $admin->assignRole('admin');

        // ── Matières premières ──────────────────────────────────
        $matieres = [
            ['nom' => 'Farine de blé',         'unite' => 'kg',   'stock_actuel' => 200, 'stock_minimum' => 50,  'prix_moyen_pondere' => 450],
            ['nom' => 'Sucre',                 'unite' => 'kg',   'stock_actuel' => 80,  'stock_minimum' => 20,  'prix_moyen_pondere' => 700],
            ['nom' => 'Levure boulangère',     'unite' => 'kg',   'stock_actuel' => 10,  'stock_minimum' => 3,   'prix_moyen_pondere' => 3500],
            ['nom' => 'Sel',                   'unite' => 'kg',   'stock_actuel' => 30,  'stock_minimum' => 5,   'prix_moyen_pondere' => 200],
            ['nom' => 'Beurre',                'unite' => 'kg',   'stock_actuel' => 25,  'stock_minimum' => 10,  'prix_moyen_pondere' => 4500],
            ['nom' => 'Huile végétale',        'unite' => 'litre','stock_actuel' => 40,  'stock_minimum' => 10,  'prix_moyen_pondere' => 1100],
            ['nom' => 'Œufs',                  'unite' => 'pièce','stock_actuel' => 300, 'stock_minimum' => 60,  'prix_moyen_pondere' => 150],
            ['nom' => 'Lait en poudre',        'unite' => 'kg',   'stock_actuel' => 15,  'stock_minimum' => 5,   'prix_moyen_pondere' => 3200],
            ['nom' => 'Margarine',             'unite' => 'kg',   'stock_actuel' => 20,  'stock_minimum' => 8,   'prix_moyen_pondere' => 2800],
            ['nom' => 'Levure chimique',       'unite' => 'kg',   'stock_actuel' => 2,   'stock_minimum' => 1,   'prix_moyen_pondere' => 4000],
            ['nom' => 'Extrait de vanille',    'unite' => 'litre','stock_actuel' => 1,   'stock_minimum' => 0.5, 'prix_moyen_pondere' => 15000],
            ['nom' => 'Cacao en poudre',       'unite' => 'kg',   'stock_actuel' => 8,   'stock_minimum' => 2,   'prix_moyen_pondere' => 5500],
        ];

        $matiereModels = [];
        foreach ($matieres as $data) {
            $matiereModels[$data['nom']] = MatierePremiere::create(array_merge($data, ['created_by' => $admin->id]));
        }

        // ── Produits finis ──────────────────────────────────────
        $produits = [
            ['nom' => 'Pain baguette 250g',    'categorie' => 'Pain',        'prix_vente' => 150],
            ['nom' => 'Pain baguette 500g',    'categorie' => 'Pain',        'prix_vente' => 250],
            ['nom' => 'Pain de mie',           'categorie' => 'Pain',        'prix_vente' => 500],
            ['nom' => 'Croissant',             'categorie' => 'Viennoiserie','prix_vente' => 300],
            ['nom' => 'Pain au chocolat',      'categorie' => 'Viennoiserie','prix_vente' => 350],
            ['nom' => 'Brioche',               'categorie' => 'Viennoiserie','prix_vente' => 400],
            ['nom' => 'Gâteau marbré',         'categorie' => 'Gâteau',      'prix_vente' => 5000],
            ['nom' => 'Beignets sucrés',       'categorie' => 'Pâtisserie',  'prix_vente' => 100],
        ];

        $produitModels = [];
        foreach ($produits as $data) {
            $produitModels[$data['nom']] = Produit::create(array_merge($data, ['created_by' => $admin->id]));
        }

        // ── Recettes ────────────────────────────────────────────
        // Recette 1 : Pain baguette
        $recette1 = Recette::create([
            'nom'                 => 'Fournée pain baguette 250g',
            'description'         => 'Recette standard pour 100 baguettes de 250g',
            'nb_pieces_attendues' => 100,
            'created_by'          => $admin->id,
        ]);
        $r1Lignes = [
            ['farine' => 'Farine de blé',    'qte' => 25],
            ['farine' => 'Levure boulangère', 'qte' => 0.5],
            ['farine' => 'Sel',               'qte' => 0.5],
        ];
        foreach ($r1Lignes as $l) {
            RecetteLigne::create([
                'recette_id'         => $recette1->id,
                'matiere_premiere_id' => $matiereModels[$l['farine']]->id,
                'quantite'           => $l['qte'],
            ]);
        }

        // Recette 2 : Croissants
        $recette2 = Recette::create([
            'nom'                 => 'Fournée croissants',
            'description'         => 'Croissants pur beurre, recette traditionnelle',
            'nb_pieces_attendues' => 60,
            'created_by'          => $admin->id,
        ]);
        $r2Lignes = [
            ['farine' => 'Farine de blé',    'qte' => 10],
            ['farine' => 'Beurre',            'qte' => 5],
            ['farine' => 'Sucre',             'qte' => 1],
            ['farine' => 'Levure boulangère', 'qte' => 0.3],
            ['farine' => 'Sel',               'qte' => 0.2],
            ['farine' => 'Lait en poudre',    'qte' => 0.5],
        ];
        foreach ($r2Lignes as $l) {
            RecetteLigne::create([
                'recette_id'         => $recette2->id,
                'matiere_premiere_id' => $matiereModels[$l['farine']]->id,
                'quantite'           => $l['qte'],
            ]);
        }

        // Recette 3 : Pain de mie
        $recette3 = Recette::create([
            'nom'                 => 'Fournée pain de mie',
            'description'         => 'Pain de mie moelleux en moule',
            'nb_pieces_attendues' => 30,
            'created_by'          => $admin->id,
        ]);
        $r3Lignes = [
            ['farine' => 'Farine de blé',  'qte' => 12],
            ['farine' => 'Sucre',           'qte' => 1.5],
            ['farine' => 'Margarine',       'qte' => 1.5],
            ['farine' => 'Lait en poudre',  'qte' => 1],
            ['farine' => 'Sel',             'qte' => 0.3],
            ['farine' => 'Levure boulangère','qte' => 0.4],
        ];
        foreach ($r3Lignes as $l) {
            RecetteLigne::create([
                'recette_id'         => $recette3->id,
                'matiere_premiere_id' => $matiereModels[$l['farine']]->id,
                'quantite'           => $l['qte'],
            ]);
        }

        $this->command->info('✅ Données initiales chargées avec succès !');
        $this->command->info('   Email admin : admin@boulangerie.ci');
        $this->command->info('   Mot de passe : password');
    }
}
