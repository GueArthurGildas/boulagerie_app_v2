@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble de votre boulangerie')

@section('content')

{{-- KPI du jour --}}
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Fournées aujourd'hui</div>
        <div class="kpi-value">{{ $stats['fournees_jour'] }}</div>
        <div class="kpi-sub">{{ $stats['fournees_terminees'] }} terminée(s)</div>
        <i class="ri-fire-line kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Pièces produites</div>
        <div class="kpi-value">{{ number_format($stats['pieces_produites']) }}</div>
        <div class="kpi-sub">Rendement moy. {{ $stats['rendement_moyen'] }}%</div>
        <i class="ri-stack-line kpi-icon"></i>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-label">Alertes stock</div>
        <div class="kpi-value">{{ $stats['alertes_stock'] }}</div>
        <div class="kpi-sub">matière(s) sous seuil</div>
        <i class="ri-alert-line kpi-icon"></i>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-label">Recettes actives</div>
        <div class="kpi-value">{{ $stats['recettes_actives'] }}</div>
        <div class="kpi-sub">{{ $stats['produits_actifs'] }} produit(s)</div>
        <i class="ri-book-2-line kpi-icon"></i>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">

    {{-- Fournées récentes --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Fournées du jour</span>
            <a href="{{ route('productions.create') }}" class="btn btn-primary btn-sm">
                <i class="ri-add-line"></i> Nouvelle
            </a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Recette</th>
                        <th>Équipe</th>
                        <th>Statut</th>
                        <th>Rendement</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions_jour as $p)
                        <tr>
                            <td>
                                <a href="{{ route('productions.show', $p) }}" style="color:var(--blanc);text-decoration:none;font-weight:500;">
                                    {{ $p->recette->nom }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $p->equipe === 'jour' ? 'badge-blue' : 'badge-gray' }}">
                                    {{ $p->equipe }}
                                </span>
                            </td>
                            <td>
                                @if($p->statut === 'terminee')
                                    <span class="badge badge-green"><i class="ri-check-line"></i> Terminée</span>
                                @elseif($p->statut === 'en_cours')
                                    <span class="badge badge-orange"><i class="ri-loader-line"></i> En cours</span>
                                @else
                                    <span class="badge badge-red">Annulée</span>
                                @endif
                            </td>
                            <td>
                                @if($p->rendement > 0)
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <div class="progress" style="width:60px">
                                            <div class="progress-bar {{ $p->rendement >= 90 ? 'green' : ($p->rendement >= 70 ? 'orange' : '') }}"
                                                 style="width:{{ min($p->rendement,100) }}%"></div>
                                        </div>
                                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--gris-light)">
                                            {{ $p->rendement }}%
                                        </span>
                                    </div>
                                @else
                                    <span style="color:var(--gris-dark)">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--gris-dark);padding:32px;">
                                <i class="ri-fire-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Aucune fournée aujourd'hui
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Alertes stock --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Alertes Matières</span>
            <a href="{{ route('matieres-premieres.index') }}" class="btn btn-outline btn-sm">Voir tout</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Stock actuel</th>
                        <th>Seuil min.</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alertes_stock as $m)
                        <tr>
                            <td style="font-weight:500">{{ $m->nom }}</td>
                            <td>
                                <span style="font-family:'DM Mono',monospace;color:var(--rouge-vif);">
                                    {{ $m->stock_actuel }} {{ $m->unite }}
                                </span>
                            </td>
                            <td style="color:var(--gris-mid)">{{ $m->stock_minimum }} {{ $m->unite }}</td>
                            <td>
                                @if($m->stock_actuel <= 0)
                                    <span class="badge badge-red">Rupture</span>
                                @else
                                    <span class="badge badge-orange">Bas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--gris-dark);padding:32px;">
                                <i class="ri-check-double-line" style="font-size:32px;display:block;margin-bottom:8px;color:var(--succes)"></i>
                                Tous les stocks sont OK
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Top produits --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Produits les plus fabriqués cette semaine</span>
    </div>
    <div class="card-body">
        @forelse($top_produits as $item)
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                <div style="width:28px;height:28px;background:var(--noir-border);border-radius:6px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:14px;color:var(--rouge);flex-shrink:0;">
                    {{ $loop->iteration }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:14px;font-weight:500;color:var(--blanc)">{{ $item->produit->nom ?? '—' }}</span>
                        <span style="font-family:'DM Mono',monospace;font-size:12px;color:var(--gris-mid)">{{ $item->total }} pcs</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar {{ $loop->first ? '' : 'green' }}"
                             style="width:{{ $top_produits->first() && $top_produits->first()->total > 0 ? ($item->total / $top_produits->first()->total * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <p style="text-align:center;color:var(--gris-dark);padding:16px 0;">Aucune donnée cette semaine</p>
        @endforelse
    </div>
</div>

@endsection
