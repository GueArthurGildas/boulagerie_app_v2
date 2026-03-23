@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('page-title', 'Fournisseurs')
@section('page-subtitle', 'Gestion des fournisseurs et achats')

@section('content')

{{-- Alerte achats en brouillon --}}
@php $achatsEnAttente = \App\Models\Achat::where('statut','brouillon')->count(); @endphp
@if($achatsEnAttente > 0)
    <div class="alert alert-warning" style="margin-bottom:20px;">
        <i class="ri-draft-line"></i>
        <div>
            <strong>{{ $achatsEnAttente }} achat(s) en brouillon</strong> — Ces achats ne sont pas encore validés.
            Le stock n'a pas été mis à jour. Consultez chaque fournisseur pour les valider.
        </div>
    </div>
@endif

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Fournisseurs actifs</div>
        <div class="kpi-value">{{ $fournisseurs->total() }}</div>
        <div class="kpi-sub">référencés</div>
        <i class="ri-truck-line kpi-icon"></i>
    </div>
    <div class="kpi-card danger">
        <div class="kpi-label">Total dû</div>
        <div class="kpi-value-currency">{{ number_format($totalDu) }}<span class="montant-unit">FCFA</span></div>
        <div class="kpi-sub">en crédit fournisseur</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-label">Achats en brouillon</div>
        <div class="kpi-value">{{ $achatsEnAttente }}</div>
        <div class="kpi-sub">à valider</div>
        <i class="ri-draft-line kpi-icon"></i>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Échéances dépassées</div>
        <div class="kpi-value">{{ \App\Models\Achat::where('statut','valide')->where('date_echeance','<',now())->count() }}</div>
        <div class="kpi-sub">achats en retard</div>
        <i class="ri-alarm-warning-line kpi-icon"></i>
    </div>
</div>

<div class="page-header">
    <div class="page-header-left">
        <h2>Liste des fournisseurs</h2>
        <div class="title-bar">
            <div class="title-bar-line"></div>
            <span class="title-bar-text">{{ $fournisseurs->total() }} fournisseur(s)</span>
        </div>
    </div>
    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-lg">
        <i class="ri-add-circle-line"></i> Nouveau fournisseur
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fournisseur</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Crédit disponible</th>
                    <th>Solde dû</th>
                    <th>Achats</th>
                    <th>Alertes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseurs as $f)
                    @php
                        $brouillons = $f->achats()->where('statut','brouillon')->count();
                        $echus      = $f->achats()->where('statut','valide')->where('date_echeance','<',now())->count();
                    @endphp
                    <tr style="{{ $brouillons > 0 ? 'background:rgba(200,150,12,.04);' : '' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                {{-- Icône fournisseur --}}
                                <div style="width:36px;height:36px;border-radius:9px;
                                    background:{{ $f->solde_du > 0 ? 'var(--danger-bg)' : 'var(--bg-surface2)' }};
                                    display:flex;align-items:center;justify-content:center;
                                    flex-shrink:0;border:1px solid {{ $f->solde_du > 0 ? 'var(--danger-border)' : 'var(--noir-border)' }};">
                                    <i class="ri-truck-line" style="font-size:17px;color:{{ $f->solde_du > 0 ? 'var(--danger)' : 'var(--noir-light)' }};"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700;color:var(--noir-text);font-size:14px;">{{ $f->nom }}</div>
                                    @if($f->ville)
                                        <div style="font-size:11px;color:var(--noir-light);margin-top:1px;">
                                            <i class="ri-map-pin-line"></i> {{ $f->ville }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($f->telephone)
                                <div style="font-size:13px;color:var(--noir-mid);display:flex;align-items:center;gap:5px;">
                                    <i class="ri-phone-line" style="color:var(--succes);"></i>
                                    <span class="montant" style="font-size:13px;font-weight:500;">{{ $f->telephone }}</span>
                                </div>
                            @endif
                            @if($f->contact_nom)
                                <div style="font-size:11px;color:var(--noir-light);margin-top:2px;">{{ $f->contact_nom }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-gray">{{ ucfirst($f->type) }}</span></td>
                        <td>
                            @if($f->plafond_credit > 0)
                                <div class="montant" style="font-size:13px;color:var(--noir-text);">
                                    {{ number_format($f->credit_disponible) }}
                                    <span class="montant-unit">FCFA</span>
                                </div>
                                <div style="margin-top:4px;">
                                    <div class="progress" style="width:80px;">
                                        <div class="progress-bar {{ $f->taux_endettement > 80 ? 'red' : ($f->taux_endettement > 50 ? 'orange' : 'green') }}"
                                             style="width:{{ min($f->taux_endettement, 100) }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span style="color:var(--noir-light);font-size:12px;">Comptant</span>
                            @endif
                        </td>
                        <td>
                            @if($f->solde_du > 0)
                                <div class="montant" style="font-size:14px;font-weight:700;color:var(--danger);">
                                    {{ number_format($f->solde_du) }}
                                    <span class="montant-unit" style="color:var(--danger);">FCFA</span>
                                </div>
                            @else
                                <span class="badge badge-green"><i class="ri-check-line"></i> Soldé</span>
                            @endif
                        </td>
                        <td>
                            <span class="montant" style="font-size:15px;color:var(--noir-text);">{{ $f->achats_count }}</span>
                        </td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                @if($brouillons > 0)
                                    <span class="badge badge-brouillon">
                                        <i class="ri-draft-line"></i> {{ $brouillons }} brouillon(s)
                                    </span>
                                @endif
                                @if($echus > 0)
                                    <span class="badge badge-red">
                                        <i class="ri-alarm-warning-line"></i> {{ $echus }} échu(s)
                                    </span>
                                @endif
                                @if(!$brouillons && !$echus)
                                    <span style="color:var(--noir-light);font-size:12px;">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="{{ route('fournisseurs.show', $f) }}" class="btn btn-outline btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('fournisseurs.edit', $f) }}" class="btn btn-outline btn-sm">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <a href="{{ route('fournisseurs.achats', $f) }}" class="btn btn-gold btn-sm" title="Nouvel achat">
                                    <i class="ri-shopping-bag-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:48px;color:var(--noir-light);">
                            <i class="ri-truck-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucun fournisseur enregistré
                            <br>
                            <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Ajouter un fournisseur
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fournisseurs->hasPages())
        <div style="padding:14px 22px;border-top:1px solid var(--noir-border);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:12px;color:var(--noir-light);">{{ $fournisseurs->total() }} fournisseur(s)</span>
            <div class="pagination">{{ $fournisseurs->links('vendor.pagination.custom') }}</div>
        </div>
    @endif
</div>
@endsection
