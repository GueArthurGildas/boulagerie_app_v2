@extends('layouts.app')
@section('title', 'Fournisseurs')
@section('page-title', 'Fournisseurs')
@section('page-subtitle', 'Gestion des fournisseurs et achats')

@section('content')

<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">Fournisseurs actifs</div>
        <div class="kpi-value">{{ $fournisseurs->total() }}</div>
        <div class="kpi-sub">référencés</div>
        <i class="ri-truck-line kpi-icon"></i>
    </div>
    <div class="kpi-card danger">
        <div class="kpi-label">Total dû</div>
        <div class="kpi-value" style="font-size:28px;">{{ number_format($totalDu) }}</div>
        <div class="kpi-sub">FCFA en crédit fournisseur</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Achats ce mois</div>
        <div class="kpi-value">{{ \App\Models\Achat::whereMonth('date_achat', now()->month)->where('statut','!=','brouillon')->count() }}</div>
        <div class="kpi-sub">bons validés</div>
        <i class="ri-shopping-bag-line kpi-icon"></i>
    </div>
    <div class="kpi-card orange">
        <div class="kpi-label">Échéances dépassées</div>
        <div class="kpi-value">{{ \App\Models\Achat::where('statut','valide')->where('date_echeance','<',now())->count() }}</div>
        <div class="kpi-sub">achats en retard</div>
        <i class="ri-alarm-warning-line kpi-icon"></i>
    </div>
</div>

<div class="page-header">
    <div>
        <h2>Liste des fournisseurs</h2>
        <div class="or-line"></div>
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
                    <th>Plafond crédit</th>
                    <th>Solde dû</th>
                    <th>Achats</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseurs as $f)
                    <tr>
                        <td>
                            <div style="font-weight:600;color:var(--noir-text);">{{ $f->nom }}</div>
                            @if($f->ville)
                                <div style="font-size:11px;color:var(--noir-light);margin-top:1px;">
                                    <i class="ri-map-pin-line"></i> {{ $f->ville }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($f->telephone)
                                <div style="font-size:13px;color:var(--noir-mid);">
                                    <i class="ri-phone-line"></i> {{ $f->telephone }}
                                </div>
                            @endif
                            @if($f->contact_nom)
                                <div style="font-size:12px;color:var(--noir-light);">{{ $f->contact_nom }}</div>
                            @endif
                        </td>
                        <td><span class="badge badge-gray">{{ ucfirst($f->type) }}</span></td>
                        <td style="font-family:'DM Mono',monospace;font-size:13px;">
                            @if($f->plafond_credit > 0)
                                {{ number_format($f->plafond_credit) }} FCFA
                            @else
                                <span style="color:var(--noir-light);">Aucun</span>
                            @endif
                        </td>
                        <td>
                            @if($f->solde_du > 0)
                                <span style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:var(--danger);">
                                    {{ number_format($f->solde_du) }} FCFA
                                </span>
                                @if($f->plafond_credit > 0)
                                    <div style="margin-top:4px;">
                                        <div class="progress" style="width:80px;">
                                            <div class="progress-bar {{ $f->taux_endettement > 80 ? '' : 'green' }}"
                                                 style="width:{{ min($f->taux_endettement, 100) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <span class="badge badge-green">Soldé</span>
                            @endif
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--noir-mid);">
                            {{ $f->achats_count }}
                        </td>
                        <td>
                            <span class="badge {{ $f->actif ? 'badge-green' : 'badge-gray' }}">
                                {{ $f->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:5px;">
                                <a href="{{ route('fournisseurs.show', $f) }}" class="btn btn-outline btn-sm">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('fournisseurs.edit', $f) }}" class="btn btn-outline btn-sm">
                                    <i class="ri-pencil-line"></i>
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
        <div style="padding:14px 22px;border-top:1px solid var(--noir-border);">
            {{ $fournisseurs->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection
