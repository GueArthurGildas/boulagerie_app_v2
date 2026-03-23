@extends('layouts.app')
@section('title', 'Dépenses')
@section('page-title', 'Dépenses')
@section('page-subtitle', 'Suivi et gestion des dépenses')

@section('content')

{{-- Alerte achats brouillon (non visibles dans les dépenses) --}}
@php $achatsEnAttente = \App\Models\Achat::where('statut','brouillon')->count(); @endphp
@if($achatsEnAttente > 0)
    <div style="margin-bottom:20px;padding:13px 16px;background:#FFF3CD;border:1.5px solid #FFD452;border-radius:10px;display:flex;align-items:center;gap:12px;">
        <i class="ri-information-line" style="font-size:18px;color:#856404;flex-shrink:0;"></i>
        <div style="flex:1;font-size:13px;color:#856404;">
            <strong>Note :</strong> {{ $achatsEnAttente }} achat(s) fournisseur en brouillon ne sont pas inclus dans les dépenses.
            Validez-les depuis la fiche fournisseur pour qu'ils apparaissent ici.
        </div>
        <a href="{{ route('fournisseurs.index') }}" style="font-size:12px;font-weight:700;color:#856404;text-decoration:none;white-space:nowrap;border:1px solid #FFD452;padding:5px 10px;border-radius:6px;">
            Voir fournisseurs →
        </a>
    </div>
@endif

{{-- KPI --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Total ce mois</div>
        <div class="kpi-value-currency">{{ number_format($totalMois) }}<span class="montant-unit">FCFA</span></div>
        <div class="kpi-sub">{{ now()->locale('fr')->isoFormat('MMMM YYYY') }}</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Validées ce mois</div>
        <div class="kpi-value">{{ \App\Models\Depense::validees()->whereMonth('date_depense', now()->month)->count() }}</div>
        <div class="kpi-sub">dépenses</div>
        <i class="ri-check-double-line kpi-icon"></i>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-label">En brouillon</div>
        <div class="kpi-value">{{ \App\Models\Depense::brouillons()->count() }}</div>
        <div class="kpi-sub">à valider</div>
        <i class="ri-draft-line kpi-icon"></i>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-label">Récurrentes</div>
        <div class="kpi-value">{{ \App\Models\Depense::where('est_recurrente', true)->count() }}</div>
        <div class="kpi-sub">modèles</div>
        <i class="ri-repeat-line kpi-icon"></i>
    </div>
</div>

<div class="page-header">
    <div class="page-header-left">
        <h2>Liste des dépenses</h2>
        <div class="title-bar">
            <div class="title-bar-line"></div>
            <span class="title-bar-text">{{ $depenses->total() }} dépense(s)</span>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('depenses.categories') }}" class="btn btn-outline">
            <i class="ri-price-tag-3-line"></i> Catégories
        </a>
        <a href="{{ route('depenses.rapport') }}" class="btn btn-outline">
            <i class="ri-bar-chart-line"></i> Rapport
        </a>
        <a href="{{ route('depenses.create') }}" class="btn btn-primary btn-lg">
            <i class="ri-add-circle-line"></i> Nouvelle dépense
        </a>
    </div>
</div>

{{-- Filtres --}}
<div class="card" style="margin-bottom:22px;">
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon gray"><i class="ri-filter-3-line"></i></div>
            <div><div class="section-title-text">Filtres</div></div>
        </div>
        <a href="{{ route('depenses.index') }}" class="btn btn-reset btn-sm">
            <i class="ri-refresh-line"></i> Réinitialiser
        </a>
    </div>
    <div style="padding:16px 22px;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:160px;">
                <label class="form-label">Recherche</label>
                <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Libellé...">
            </div>
            <div style="flex:1;min-width:140px;">
                <label class="form-label">Catégorie</label>
                <select name="categorie" class="form-control">
                    <option value="">Toutes</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('categorie') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:130px;">
                <label class="form-label">Mode paiement</label>
                <select name="mode" class="form-control">
                    <option value="">Tous</option>
                    @foreach(['cash'=>'Cash','orange_money'=>'Orange Money','wave'=>'Wave','mtn_momo'=>'MTN MoMo','banque'=>'Banque'] as $val => $label)
                        <option value="{{ $val }}" {{ request('mode') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:120px;">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control">
                    <option value="">Tous</option>
                    <option value="validee"   {{ request('statut') === 'validee'   ? 'selected' : '' }}>Validée</option>
                    <option value="brouillon" {{ request('statut') === 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="rejetee"   {{ request('statut') === 'rejetee'   ? 'selected' : '' }}>Rejetée</option>
                </select>
            </div>
            <div style="flex:1;min-width:120px;">
                <label class="form-label">Du</label>
                <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            <div style="flex:1;min-width:120px;">
                <label class="form-label">Au</label>
                <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="ri-search-line"></i> Filtrer</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Bénéficiaire</th>
                    <th>Mode</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($depenses as $depense)
                    <tr>
                        <td style="font-family:var(--font-mono);color:var(--noir-light);font-size:11px;font-weight:500;">#{{ $depense->id }}</td>
                        <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;white-space:nowrap;">
                            {{ $depense->date_depense->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="font-weight:700;color:var(--noir-text);font-size:13px;">{{ $depense->libelle }}</div>
                            @if($depense->est_recurrente)
                                <div style="font-size:10px;color:var(--or);margin-top:2px;font-weight:600;">
                                    <i class="ri-repeat-line"></i> {{ ucfirst($depense->frequence_recurrence) }}
                                </div>
                            @endif
                            @if($depense->fournisseur)
                                <div style="font-size:10px;color:var(--info);margin-top:2px;">
                                    <i class="ri-truck-line"></i> {{ $depense->fournisseur->nom }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($depense->categorie)
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--noir-mid);">
                                    <span style="width:8px;height:8px;border-radius:50%;background:{{ $depense->categorie->couleur }};flex-shrink:0;"></span>
                                    {{ $depense->categorie->nom }}
                                </span>
                            @endif
                        </td>
                        <td style="color:var(--noir-mid);font-size:13px;">{{ $depense->beneficiaire ?? '—' }}</td>
                        <td>
                            @php
                                $modeClass = match($depense->mode_paiement) {
                                    'cash'         => 'badge-green',
                                    'orange_money' => 'badge-orange',
                                    'wave'         => 'badge-blue',
                                    'mtn_momo'     => 'badge-gold',
                                    'banque'       => 'badge-gray',
                                    default        => 'badge-gray',
                                };
                            @endphp
                            <span class="badge {{ $modeClass }}">{{ $depense->mode_libelle }}</span>
                        </td>
                        <td style="white-space:nowrap;">
                            <span class="montant" style="font-size:15px;font-weight:700;color:var(--noir-text);">
                                {{ number_format($depense->montant) }}
                            </span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td>
                            @if($depense->statut === 'validee')
                                <span class="badge badge-green"><i class="ri-check-line"></i> Validée</span>
                            @elseif($depense->statut === 'brouillon')
                                <span class="badge badge-brouillon"><i class="ri-draft-line"></i> Brouillon</span>
                            @else
                                <span class="badge badge-red"><i class="ri-close-line"></i> Rejetée</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="{{ route('depenses.show', $depense) }}" class="btn btn-outline btn-sm"><i class="ri-eye-line"></i></a>
                                @if($depense->statut === 'brouillon')
                                    <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-outline btn-sm"><i class="ri-pencil-line"></i></a>
                                    <form method="POST" action="{{ route('depenses.valider', $depense) }}">
                                        @csrf
                                        <button class="btn btn-success btn-sm"><i class="ri-check-double-line"></i></button>
                                    </form>
                                @endif
                                @if($depense->est_recurrente)
                                    <form method="POST" action="{{ route('depenses.cloner', $depense) }}">
                                        @csrf
                                        <button class="btn btn-gold btn-sm" title="Cloner"><i class="ri-file-copy-line"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:48px;color:var(--noir-light);">
                            <i class="ri-money-dollar-circle-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucune dépense trouvée
                            <br>
                            <a href="{{ route('depenses.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Ajouter une dépense
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($depenses->hasPages())
        <div style="padding:14px 22px;border-top:1px solid var(--noir-border);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:var(--noir-light);">{{ $depenses->total() }} dépense(s)</span>
            <div class="pagination">{{ $depenses->links('vendor.pagination.custom') }}</div>
        </div>
    @endif
</div>
@endsection
