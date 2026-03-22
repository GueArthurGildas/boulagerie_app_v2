@extends('layouts.app')

@section('title', 'Fournée #' . $production->id)
@section('page-title', 'Fournée #' . $production->id)
@section('page-subtitle', $production->recette->nom . ' — ' . $production->date_production->format('d/m/Y'))

@push('styles')
<style>
.action-panel {
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid;
}
.action-panel.panel-correction { background: #EBF4FD; border-color: rgba(30,111,168,.2); }
.action-panel.panel-invendus   { background: #FEF8E7; border-color: rgba(200,150,12,.25); }
.action-panel.panel-annulation { background: #FDECEB; border-color: rgba(192,57,43,.2);  }

.panel-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px; font-weight: 600;
    margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.panel-correction .panel-title { color: var(--info); }
.panel-invendus .panel-title   { color: var(--or); }
.panel-annulation .panel-title { color: var(--danger); }

.toggle-panel-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 7px;
    font-size: 12px; font-weight: 600;
    cursor: pointer; border: 1px solid; transition: all .18s;
    background: transparent;
}
.btn-toggle-correction { color: var(--info);   border-color: rgba(30,111,168,.3); }
.btn-toggle-correction:hover { background: var(--info-bg); }
.btn-toggle-invendus   { color: var(--or);     border-color: var(--or-border); }
.btn-toggle-invendus:hover { background: var(--or-pale); }
.btn-toggle-annulation { color: var(--danger); border-color: rgba(192,57,43,.3); }
.btn-toggle-annulation:hover { background: var(--danger-bg); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2>Fournée #{{ $production->id }}</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">{{ $production->recette->nom }} — {{ $production->date_production->format('d/m/Y') }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('productions.index') }}" class="btn btn-outline">
            <i class="ri-arrow-left-line"></i> Retour
        </a>

        @if($production->statut === 'en_cours')
            <span class="badge badge-orange" style="padding:8px 14px;font-size:12px;">
                <i class="ri-loader-line"></i> En cours
            </span>
        @elseif($production->statut === 'terminee')
            <span class="badge badge-green" style="padding:8px 14px;font-size:12px;">
                <i class="ri-check-double-line"></i> Terminée
            </span>
        @elseif($production->statut === 'annulee')
            <span class="badge badge-red" style="padding:8px 14px;font-size:12px;">
                <i class="ri-close-circle-line"></i> Annulée
            </span>
        @endif

        {{-- Boutons d'action selon statut --}}
        @if($production->statut === 'terminee')
            <button class="toggle-panel-btn btn-toggle-correction"
                    onclick="togglePanel('panel-correction')">
                <i class="ri-edit-2-line"></i> Corriger
            </button>
            <button class="toggle-panel-btn btn-toggle-invendus"
                    onclick="togglePanel('panel-invendus')">
                <i class="ri-store-2-line"></i> Invendus
            </button>
        @endif

        @if(in_array($production->statut, ['en_cours', 'terminee']))
            <button class="toggle-panel-btn btn-toggle-annulation"
                    onclick="togglePanel('panel-annulation')">
                <i class="ri-forbid-line"></i> Annuler
            </button>
        @endif
    </div>
</div>

{{-- ─── PANEL CORRECTION ─── --}}
@if($production->statut === 'terminee')
<div id="panel-correction" class="action-panel panel-correction" style="display:none;">
    <div class="panel-title">
        <i class="ri-edit-2-line"></i> Corriger les quantités produites
    </div>
    <p style="font-size:13px;color:var(--noir-mid);margin-bottom:16px;">
        ⚠️ Cette correction ne modifie pas le stock — uniquement les chiffres de production.
    </p>
    <form method="POST" action="{{ route('productions.correct', $production) }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Motif de la correction *</label>
            <input type="text" name="motif_correction" class="form-control"
                   placeholder="Ex: Erreur de saisie lors de la clôture" required minlength="5">
        </div>
        <div class="table-wrap" style="margin-bottom:16px;">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Qté produite</th>
                        <th>Qté invendue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produits as $i => $produit)
                        @php $ligne = $production->lignes->firstWhere('produit_id', $produit->id); @endphp
                        <tr>
                            <td>
                                <input type="hidden" name="lignes[{{ $i }}][produit_id]" value="{{ $produit->id }}">
                                <span style="font-weight:500;color:var(--noir-text)">{{ $produit->nom }}</span>
                            </td>
                            <td>
                                <input type="number" name="lignes[{{ $i }}][quantite_produite]"
                                       class="form-control" style="width:100px;"
                                       min="0" value="{{ $ligne->quantite_produite ?? 0 }}">
                            </td>
                            <td>
                                <input type="number" name="lignes[{{ $i }}][quantite_invendue]"
                                       class="form-control" style="width:100px;"
                                       min="0" value="{{ $ligne->quantite_invendue ?? 0 }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Enregistrer la correction
            </button>
            <button type="button" class="btn btn-outline" onclick="togglePanel('panel-correction')">Annuler</button>
        </div>
    </form>
</div>

{{-- ─── PANEL INVENDUS ─── --}}
<div id="panel-invendus" class="action-panel panel-invendus" style="display:none;">
    <div class="panel-title">
        <i class="ri-store-2-line"></i> Modifier les invendus
    </div>
    <p style="font-size:13px;color:var(--noir-mid);margin-bottom:16px;">
        Ajustez uniquement les quantités invendues sans modifier les quantités produites.
    </p>
    <form method="POST" action="{{ route('productions.update-invendus', $production) }}">
        @csrf
        <div class="table-wrap" style="margin-bottom:16px;">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Produite</th>
                        <th>Invendue actuelle</th>
                        <th>Nouvelle valeur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($production->lignes as $i => $ligne)
                        <tr>
                            <td>
                                <input type="hidden" name="lignes[{{ $i }}][ligne_id]" value="{{ $ligne->id }}">
                                <span style="font-weight:500;color:var(--noir-text)">{{ $ligne->produit->nom }}</span>
                            </td>
                            <td style="font-family:'DM Mono',monospace;color:var(--noir-mid)">
                                {{ $ligne->quantite_produite }}
                            </td>
                            <td style="font-family:'DM Mono',monospace;color:var(--warning)">
                                {{ $ligne->quantite_invendue }}
                            </td>
                            <td>
                                <input type="number" name="lignes[{{ $i }}][quantite_invendue]"
                                       class="form-control" style="width:100px;"
                                       min="0" max="{{ $ligne->quantite_produite }}"
                                       value="{{ $ligne->quantite_invendue }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-gold">
                <i class="ri-save-line"></i> Mettre à jour les invendus
            </button>
            <button type="button" class="btn btn-outline" onclick="togglePanel('panel-invendus')">Annuler</button>
        </div>
    </form>
</div>
@endif

{{-- ─── PANEL ANNULATION ─── --}}
@if(in_array($production->statut, ['en_cours', 'terminee']))
<div id="panel-annulation" class="action-panel panel-annulation" style="display:none;">
    <div class="panel-title">
        <i class="ri-forbid-line"></i> Annuler cette fournée
    </div>
    <p style="font-size:13px;color:var(--noir-mid);margin-bottom:16px;">
        ⚠️ <strong>Action irréversible.</strong> Le stock des matières premières consommées sera <strong>restitué automatiquement</strong>.
        @if($production->statut === 'terminee')
            <br><span style="color:var(--danger)">Attention : cette fournée est déjà terminée. Vérifiez qu'aucune vente n'a été réalisée sur ces produits.</span>
        @endif
    </p>
    <form method="POST" action="{{ route('productions.annuler', $production) }}"
          onsubmit="return confirm('Confirmer l\'annulation ? Le stock sera restitué.')">
        @csrf
        <div class="form-group" style="max-width:500px;">
            <label class="form-label">Motif d'annulation *</label>
            <input type="text" name="motif_annulation" class="form-control"
                   placeholder="Ex: Mauvaise recette sélectionnée, erreur opérateur..."
                   required minlength="5">
        </div>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-danger">
                <i class="ri-forbid-line"></i> Confirmer l'annulation
            </button>
            <button type="button" class="btn btn-outline" onclick="togglePanel('panel-annulation')">Retour</button>
        </div>
    </form>
</div>
@endif

{{-- ─── INFOS + MATIÈRES ─── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div class="form-label">Recette</div>
                    <div style="font-weight:600;color:var(--noir-text);">{{ $production->recette->nom }}</div>
                </div>
                <div>
                    <div class="form-label">Équipe</div>
                    <span class="badge {{ $production->equipe === 'jour' ? 'badge-blue' : 'badge-gray' }}">
                        <i class="ri-{{ $production->equipe === 'jour' ? 'sun' : 'moon' }}-line"></i>
                        {{ ucfirst($production->equipe) }}
                    </span>
                </div>
                <div>
                    <div class="form-label">Date</div>
                    <div style="font-family:'DM Mono',monospace;color:var(--noir-text);">
                        {{ $production->date_production->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <div class="form-label">Pièces attendues</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--noir-text);">
                        {{ $production->nb_pieces_attendues }}
                    </div>
                </div>
                @if($production->statut === 'terminee')
                    <div>
                        <div class="form-label">Pièces produites</div>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--succes);">
                            {{ $production->nb_pieces_produites }}
                        </div>
                    </div>
                    <div>
                        <div class="form-label">Rendement</div>
                        <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;
                            color:{{ $production->rendement >= 90 ? 'var(--succes)' : ($production->rendement >= 70 ? 'var(--or)' : 'var(--danger)') }}">
                            {{ $production->rendement }}%
                        </div>
                    </div>
                @endif
            </div>
            @if($production->notes)
                <hr class="divider">
                <div class="form-label">Notes</div>
                <p style="font-size:13px;color:var(--noir-mid);line-height:1.7;white-space:pre-line;">{{ $production->notes }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Matières consommées</span>
            @if($production->statut === 'annulee')
                <span class="badge badge-green">Restituées</span>
            @endif
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Consommée</th>
                        <th>Unité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($production->recette->lignes as $ligne)
                        <tr>
                            <td style="font-weight:500">{{ $ligne->matierePremiere->nom }}</td>
                            <td style="font-family:'DM Mono',monospace;
                                color:{{ $production->statut === 'annulee' ? 'var(--succes)' : 'var(--danger)' }}">
                                {{ $production->statut === 'annulee' ? '+' : '-' }}{{ $ligne->quantite }}
                            </td>
                            <td style="color:var(--noir-light)">{{ $ligne->matierePremiere->unite }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ─── CLÔTURE (fournée en cours) ─── --}}
@if($production->statut === 'en_cours')
<div class="card" style="margin-bottom:22px;border-color:rgba(26,138,74,.3);">
    <div class="card-header" style="background:var(--succes-bg);">
        <span class="card-title" style="color:var(--succes);">
            <i class="ri-check-double-line"></i> Clôturer la fournée
        </span>
    </div>
    <div class="card-body">
        <p style="font-size:13px;color:var(--noir-mid);margin-bottom:18px;">
            Saisissez les quantités produites et invendues pour finaliser cette fournée.
        </p>
        <form method="POST" action="{{ route('productions.close', $production) }}">
            @csrf
            <div class="table-wrap" style="margin-bottom:18px;">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Qté produite</th>
                            <th>Qté invendue</th>
                            <th>Vendue (calc.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produits as $i => $produit)
                            <tr>
                                <td>
                                    <input type="hidden" name="lignes[{{ $i }}][produit_id]" value="{{ $produit->id }}">
                                    <span style="font-weight:500;color:var(--noir-text)">{{ $produit->nom }}</span>
                                </td>
                                <td>
                                    <input type="number" name="lignes[{{ $i }}][quantite_produite]"
                                           class="form-control qte-produite" style="width:100px;"
                                           min="0" value="0" data-row="{{ $i }}">
                                </td>
                                <td>
                                    <input type="number" name="lignes[{{ $i }}][quantite_invendue]"
                                           class="form-control qte-invendue" style="width:100px;"
                                           min="0" value="0" data-row="{{ $i }}">
                                </td>
                                <td>
                                    <span id="vendue_{{ $i }}"
                                          style="font-family:'DM Mono',monospace;color:var(--succes);font-size:14px;">
                                        0
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('productions.index') }}" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ri-check-double-line"></i> Valider la clôture
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- ─── DÉTAIL PRODUCTION (terminée) ─── --}}
@if($production->statut === 'terminee' && $production->lignes->count())
<div class="card" style="margin-bottom:22px;">
    <div class="card-header">
        <span class="card-title">Détail de production</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Produite</th>
                    <th>Invendue</th>
                    <th>Vendue</th>
                    <th>Taux vente</th>
                </tr>
            </thead>
            <tbody>
                @foreach($production->lignes as $ligne)
                    @php $vendue = $ligne->quantite_produite - $ligne->quantite_invendue; @endphp
                    <tr>
                        <td style="font-weight:500;color:var(--noir-text)">{{ $ligne->produit->nom }}</td>
                        <td style="font-family:'DM Mono',monospace;">{{ $ligne->quantite_produite }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--warning);">{{ $ligne->quantite_invendue }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--succes);">{{ $vendue }}</td>
                        <td>
                            @php $taux = $ligne->quantite_produite > 0 ? round($vendue / $ligne->quantite_produite * 100) : 0; @endphp
                            <div style="display:flex;align-items:center;gap:8px;min-width:100px;">
                                <div class="progress" style="width:60px">
                                    <div class="progress-bar {{ $taux >= 80 ? 'green' : 'orange' }}"
                                         style="width:{{ $taux }}%"></div>
                                </div>
                                <span style="font-size:12px;color:var(--noir-light);">{{ $taux }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ─── INCIDENTS ─── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Incidents</span>
        @if($production->statut === 'en_cours')
            <button class="btn btn-outline btn-sm" onclick="togglePanel('panel-incident')">
                <i class="ri-add-line"></i> Signaler
            </button>
        @endif
    </div>

    @if($production->statut === 'en_cours')
    <div id="panel-incident" style="display:none;padding:18px 22px;border-bottom:1px solid var(--noir-border);background:var(--danger-bg);">
        <form method="POST" action="{{ route('productions.incidents.store', $production) }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Type d'incident *</label>
                    <select name="type_incident" class="form-control" required>
                        <option value="">— Choisir —</option>
                        <option value="panne_four">Panne four</option>
                        <option value="fournee_ratee">Fournée ratée</option>
                        <option value="coupure_courant">Coupure courant</option>
                        <option value="manque_ingredient">Manque ingrédient</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Durée arrêt (min)</label>
                    <input type="number" name="duree_arret_minutes" class="form-control" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Impact estimé (FCFA)</label>
                    <input type="number" name="impact_fcfa" class="form-control" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Détails...">
                </div>
            </div>
            <button type="submit" class="btn btn-danger">
                <i class="ri-alert-line"></i> Enregistrer l'incident
            </button>
        </form>
    </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Durée</th>
                    <th>Impact</th>
                </tr>
            </thead>
            <tbody>
                @forelse($production->incidents as $incident)
                    <tr>
                        <td><span class="badge badge-red">{{ str_replace('_',' ',$incident->type_incident) }}</span></td>
                        <td style="color:var(--noir-mid)">{{ $incident->description ?? '—' }}</td>
                        <td style="font-family:'DM Mono',monospace;">{{ $incident->duree_arret_minutes }} min</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--danger);">
                            {{ number_format($incident->impact_fcfa) }} FCFA
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:24px;color:var(--noir-light);">
                            Aucun incident signalé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePanel(id) {
    const panels = ['panel-correction','panel-invendus','panel-annulation','panel-incident'];
    panels.forEach(p => {
        const el = document.getElementById(p);
        if (el) {
            if (p === id) {
                el.style.display = el.style.display === 'none' ? 'block' : 'none';
            } else {
                el.style.display = 'none';
            }
        }
    });
    if (document.getElementById(id)?.style.display === 'block') {
        document.getElementById(id).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// Calcul dynamique vendu (clôture)
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qte-produite') || e.target.classList.contains('qte-invendue')) {
        const row = e.target.dataset.row;
        const produite = parseInt(document.querySelector(`.qte-produite[data-row="${row}"]`)?.value) || 0;
        const invendue = parseInt(document.querySelector(`.qte-invendue[data-row="${row}"]`)?.value) || 0;
        const el = document.getElementById(`vendue_${row}`);
        if (el) el.textContent = Math.max(0, produite - invendue);
    }
});
</script>
@endpush
