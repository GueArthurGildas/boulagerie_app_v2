@extends('layouts.app')

@section('title', 'Fournée #' . $production->id)
@section('page-title', 'Fournée #' . $production->id)
@section('page-subtitle', $production->recette->nom . ' — ' . $production->date_production->format('d/m/Y'))

@section('content')

<div class="page-header">
    <div>
        <h2>Fournée #{{ $production->id }}</h2>
        <p>{{ $production->recette->nom }} — {{ $production->date_production->format('d/m/Y') }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('productions.index') }}" class="btn btn-outline">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
        @if($production->statut === 'en_cours')
            <span class="badge badge-orange" style="padding:8px 14px;font-size:13px;">
                <i class="ri-loader-line"></i> En cours
            </span>
        @elseif($production->statut === 'terminee')
            <span class="badge badge-green" style="padding:8px 14px;font-size:13px;">
                <i class="ri-check-double-line"></i> Terminée
            </span>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">

    {{-- Infos production --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div class="form-label">Recette</div>
                    <div style="font-weight:600;color:var(--blanc);">{{ $production->recette->nom }}</div>
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
                    <div style="font-family:'DM Mono',monospace;color:var(--blanc);">{{ $production->date_production->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="form-label">Pièces attendues</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--blanc);">{{ $production->nb_pieces_attendues }}</div>
                </div>
                @if($production->statut === 'terminee')
                <div>
                    <div class="form-label">Pièces produites</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:var(--succes);">{{ $production->nb_pieces_produites }}</div>
                </div>
                <div>
                    <div class="form-label">Rendement</div>
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:24px;color:{{ $production->rendement >= 90 ? 'var(--succes)' : ($production->rendement >= 70 ? 'var(--warning)' : 'var(--rouge-vif)') }};">
                        {{ $production->rendement }}%
                    </div>
                </div>
                @endif
            </div>
            @if($production->notes)
                <hr class="divider">
                <div class="form-label">Notes</div>
                <p style="font-size:14px;color:var(--gris-light);line-height:1.6;">{{ $production->notes }}</p>
            @endif
        </div>
    </div>

    {{-- Matières consommées --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Matières consommées</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Qté consommée</th>
                        <th>Unité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($production->recette->lignes as $ligne)
                        <tr>
                            <td style="font-weight:500">{{ $ligne->matierePremiere->nom }}</td>
                            <td style="font-family:'DM Mono',monospace;color:var(--rouge-vif);">-{{ $ligne->quantite }}</td>
                            <td style="color:var(--gris-mid)">{{ $ligne->matierePremiere->unite }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Clôture fournée --}}
@if($production->statut === 'en_cours')
<div class="card" style="margin-bottom:24px;border-color:var(--rouge);">
    <div class="card-header" style="background:rgba(192,57,43,.05);">
        <span class="card-title" style="color:var(--rouge-vif);">
            <i class="ri-check-double-line"></i> Clôturer la fournée
        </span>
    </div>
    <div class="card-body">
        <p style="font-size:14px;color:var(--gris-light);margin-bottom:20px;">
            Saisissez les quantités produites et les invendus par produit pour finaliser la fournée.
        </p>

        <form method="POST" action="{{ route('productions.close', $production) }}">
            @csrf

            <div class="table-wrap" style="margin-bottom:20px;">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Qté produite</th>
                            <th>Qté invendue</th>
                            <th>Vendue (calc.)</th>
                        </tr>
                    </thead>
                    <tbody id="lignesBody">
                        @foreach($produits as $i => $produit)
                            <tr>
                                <td>
                                    <input type="hidden" name="lignes[{{ $i }}][produit_id]" value="{{ $produit->id }}">
                                    <span style="font-weight:500;color:var(--blanc)">{{ $produit->nom }}</span>
                                </td>
                                <td>
                                    <input type="number"
                                           name="lignes[{{ $i }}][quantite_produite]"
                                           class="form-control qte-produite"
                                           style="width:100px;"
                                           min="0" value="0"
                                           data-row="{{ $i }}">
                                </td>
                                <td>
                                    <input type="number"
                                           name="lignes[{{ $i }}][quantite_invendue]"
                                           class="form-control qte-invendue"
                                           style="width:100px;"
                                           min="0" value="0"
                                           data-row="{{ $i }}">
                                </td>
                                <td>
                                    <span class="vendue-calc" id="vendue_{{ $i }}"
                                          style="font-family:'DM Mono',monospace;color:var(--succes);font-size:14px;">
                                        0
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <a href="{{ route('productions.index') }}" class="btn btn-outline">
                    <i class="ri-close-line"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ri-check-double-line"></i> Valider la clôture
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Lignes produites (si terminée) --}}
@if($production->statut === 'terminee' && $production->lignes->count())
<div class="card" style="margin-bottom:24px;">
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
                        <td style="font-weight:500">{{ $ligne->produit->nom }}</td>
                        <td style="font-family:'DM Mono',monospace;">{{ $ligne->quantite_produite }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--warning);">{{ $ligne->quantite_invendue }}</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--succes);">{{ $vendue }}</td>
                        <td>
                            @php $taux = $ligne->quantite_produite > 0 ? round($vendue / $ligne->quantite_produite * 100) : 0; @endphp
                            <div style="display:flex;align-items:center;gap:8px;min-width:100px;">
                                <div class="progress" style="width:60px">
                                    <div class="progress-bar {{ $taux >= 80 ? 'green' : 'orange' }}" style="width:{{ $taux }}%"></div>
                                </div>
                                <span style="font-size:12px;color:var(--gris-light);">{{ $taux }}%</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Incidents --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Incidents</span>
        @if($production->statut === 'en_cours')
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('formIncident').classList.toggle('hidden')">
                <i class="ri-add-line"></i> Signaler
            </button>
        @endif
    </div>

    @if($production->statut === 'en_cours')
    <div id="formIncident" class="hidden" style="padding:20px;border-bottom:1px solid var(--noir-border);">
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
                    <th>Signalé par</th>
                </tr>
            </thead>
            <tbody>
                @forelse($production->incidents as $incident)
                    <tr>
                        <td>
                            <span class="badge badge-red">{{ str_replace('_', ' ', $incident->type_incident) }}</span>
                        </td>
                        <td style="color:var(--gris-light)">{{ $incident->description ?? '—' }}</td>
                        <td style="font-family:'DM Mono',monospace;">{{ $incident->duree_arret_minutes }} min</td>
                        <td style="font-family:'DM Mono',monospace;color:var(--rouge-vif);">
                            {{ number_format($incident->impact_fcfa) }} FCFA
                        </td>
                        <td style="color:var(--gris-mid);font-size:12px;">{{ $incident->createdBy->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:var(--gris-dark);">
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
    // Calcul dynamique vendu
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('qte-produite') || e.target.classList.contains('qte-invendue')) {
            const row = e.target.dataset.row;
            const produite = parseInt(document.querySelector(`.qte-produite[data-row="${row}"]`).value) || 0;
            const invendue = parseInt(document.querySelector(`.qte-invendue[data-row="${row}"]`).value) || 0;
            const vendue = Math.max(0, produite - invendue);
            document.getElementById(`vendue_${row}`).textContent = vendue;
        }
    });

    // Toggle incident form
    document.querySelectorAll('.hidden').forEach(el => el.style.display = 'none');
    const formIncident = document.getElementById('formIncident');
    if (formIncident) {
        formIncident.classList.remove('hidden');
        formIncident.style.display = 'none';
    }
    window.toggleIncident = function() {
        if (formIncident) {
            formIncident.style.display = formIncident.style.display === 'none' ? 'block' : 'none';
        }
    };
</script>
@endpush
