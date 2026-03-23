@extends('layouts.app')
@section('title', $fournisseur->nom)
@section('page-title', $fournisseur->nom)
@section('page-subtitle', ucfirst($fournisseur->type) . ' · ' . ($fournisseur->ville ?? 'Ville non renseignée'))

@section('content')

{{-- Alerte brouillons en suspens --}}
@php $brouillons = $fournisseur->achats()->where('statut','brouillon')->get(); @endphp
@if($brouillons->count() > 0)
    <div style="margin-bottom:20px;padding:14px 18px;background:#FFF3CD;border:1.5px solid #FFD452;border-radius:10px;display:flex;align-items:flex-start;gap:12px;">
        <div style="width:36px;height:36px;background:#FFD452;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ri-draft-line" style="font-size:18px;color:#856404;"></i>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700;color:#856404;font-size:14px;margin-bottom:4px;">
                {{ $brouillons->count() }} achat(s) en attente de validation
            </div>
            <div style="font-size:12px;color:#A07800;">
                @foreach($brouillons as $b)
                    <span style="margin-right:12px;">
                        <i class="ri-calendar-line"></i> {{ $b->date_achat->format('d/m/Y') }}
                        — <span class="montant" style="font-size:12px;">{{ number_format($b->lignes->sum(fn($l)=>intval($l->quantite*$l->prix_unitaire))) }}</span> FCFA
                        <a href="{{ route('fournisseurs.achat.show', [$fournisseur, $b]) }}"
                           style="color:#856404;font-weight:700;text-decoration:underline;margin-left:4px;">Voir →</a>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="page-header">
    <div class="page-header-left">
        <h2>{{ $fournisseur->nom }}</h2>
        <div class="title-bar">
            <div class="title-bar-line"></div>
            <span class="title-bar-text">{{ ucfirst($fournisseur->type) }}</span>
        </div>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('fournisseurs.index') }}" class="btn btn-back">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
        <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-outline">
            <i class="ri-pencil-line"></i> Modifier
        </a>
        <a href="{{ route('fournisseurs.achats', $fournisseur) }}" class="btn btn-primary">
            <i class="ri-shopping-bag-line"></i> Nouvel achat
        </a>
    </div>
</div>

{{-- KPI fournisseur --}}
<div class="kpi-grid" style="margin-bottom:22px;">
    <div class="kpi-card">
        <div class="kpi-label">Total achats</div>
        <div class="kpi-value-currency">{{ number_format($totalAchats) }}<span class="montant-unit">FCFA</span></div>
        <div class="kpi-sub">cumulés</div>
        <i class="ri-shopping-bag-line kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Total réglé</div>
        <div class="kpi-value-currency">{{ number_format($totalReglements) }}<span class="montant-unit">FCFA</span></div>
        <div class="kpi-sub">payés</div>
        <i class="ri-check-double-line kpi-icon"></i>
    </div>
    <div class="kpi-card {{ $fournisseur->solde_du > 0 ? 'danger' : 'green' }}">
        <div class="kpi-label">Solde dû</div>
        <div class="kpi-value-currency" style="color:{{ $fournisseur->solde_du > 0 ? 'var(--danger)' : 'var(--succes)' }};">
            {{ number_format($fournisseur->solde_du) }}<span class="montant-unit">FCFA</span>
        </div>
        <div class="kpi-sub">{{ $fournisseur->solde_du > 0 ? 'à rembourser' : 'tout soldé ✓' }}</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    @if($fournisseur->plafond_credit > 0)
    <div class="kpi-card">
        <div class="kpi-label">Crédit disponible</div>
        <div class="kpi-value-currency">{{ number_format($fournisseur->credit_disponible) }}<span class="montant-unit">FCFA</span></div>
        <div class="kpi-sub">sur {{ number_format($fournisseur->plafond_credit) }} FCFA</div>
        <i class="ri-bank-line kpi-icon"></i>
    </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">
    {{-- Coordonnées --}}
    <div class="card">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon gray"><i class="ri-contacts-line"></i></div>
                <div>
                    <div class="section-title-text">Coordonnées</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                @foreach([
                    [$fournisseur->telephone, 'ri-phone-line', 'var(--succes)', 'Téléphone'],
                    [$fournisseur->email,     'ri-mail-line',  'var(--info)',   'Email'],
                    [$fournisseur->contact_nom,'ri-user-line', 'var(--or)',     'Contact'],
                ] as [$val, $icon, $color, $label])
                    @if($val)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;background:var(--bg-surface);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="{{ $icon }}" style="color:{{ $color }};font-size:15px;"></i>
                            </div>
                            <div>
                                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--noir-light);">{{ $label }}</div>
                                <div style="font-weight:600;color:var(--noir-text);font-size:13px;">{{ $val }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
                @if($fournisseur->adresse || $fournisseur->ville)
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:30px;height:30px;background:var(--bg-surface);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-map-pin-line" style="color:var(--danger);font-size:15px;"></i>
                        </div>
                        <div>
                            <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--noir-light);">Adresse</div>
                            <div style="font-weight:500;color:var(--noir-text);font-size:13px;">{{ $fournisseur->adresse }} {{ $fournisseur->ville ? '— ' . $fournisseur->ville : '' }}</div>
                        </div>
                    </div>
                @endif
            </div>
            @if($fournisseur->notes)
                <hr class="divider">
                <div style="font-size:10px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--noir-light);margin-bottom:6px;">Notes</div>
                <p style="font-size:13px;color:var(--noir-mid);line-height:1.7;">{{ $fournisseur->notes }}</p>
            @endif
        </div>
    </div>

    {{-- Règlement --}}
    @if($fournisseur->solde_du > 0)
    <div class="card" style="border-color:var(--danger-border);">
        <div class="section-header" style="background:var(--danger-bg);">
            <div class="section-title">
                <div class="section-title-icon red"><i class="ri-money-dollar-circle-line"></i></div>
                <div>
                    <div class="section-title-text" style="color:var(--danger);">Effectuer un règlement</div>
                    <div class="section-title-sub">Solde dû : <span class="montant">{{ number_format($fournisseur->solde_du) }}</span> FCFA</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('fournisseurs.reglement.store', $fournisseur) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Achat concerné (optionnel)</label>
                    <select name="achat_id" class="form-control">
                        <option value="">— Règlement général —</option>
                        @foreach($fournisseur->achats->where('statut','!=','solde')->where('statut','!=','brouillon') as $a)
                            <option value="{{ $a->id }}">
                                {{ $a->date_achat->format('d/m/Y') }} — {{ number_format($a->montant_reste) }} FCFA
                                @if($a->reference) ({{ $a->reference }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Montant (FCFA) *</label>
                        <input type="number" name="montant" class="form-control" value="{{ $fournisseur->solde_du }}" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date_reglement" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mode paiement *</label>
                        <select name="mode_paiement" class="form-control" required>
                            @foreach(['cash'=>'Cash','orange_money'=>'Orange Money','wave'=>'Wave','mtn_momo'=>'MTN MoMo','banque'=>'Banque'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Référence mobile</label>
                        <input type="text" name="reference_mobile" class="form-control" placeholder="Optionnel">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="ri-check-double-line"></i> Enregistrer le règlement
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="card">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon gray"><i class="ri-link"></i></div>
                <div><div class="section-title-text">Lier une dépense</div></div>
            </div>
        </div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--noir-mid);margin-bottom:16px;">
                Associez manuellement une dépense existante à ce fournisseur.
            </p>
            <form method="POST" action="{{ route('fournisseurs.lier-depense', $fournisseur) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Dépense à lier</label>
                    <select name="depense_id" class="form-control" required>
                        <option value="">— Choisir —</option>
                        @foreach(\App\Models\Depense::whereNull('fournisseur_id')->orderByDesc('date_depense')->limit(50)->get() as $d)
                            <option value="{{ $d->id }}">{{ $d->date_depense->format('d/m/Y') }} — {{ $d->libelle }} ({{ number_format($d->montant) }} FCFA)</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-outline" style="width:100%;"><i class="ri-link"></i> Lier</button>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- Achats récents --}}
<div class="card" style="margin-bottom:22px;">
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon gold"><i class="ri-shopping-bag-line"></i></div>
            <div>
                <div class="section-title-text">Derniers achats</div>
                <div class="section-title-sub">{{ $fournisseur->achats->count() }} achat(s) total</div>
            </div>
        </div>
        <a href="{{ route('fournisseurs.achats', $fournisseur) }}" class="btn btn-gold btn-sm">
            <i class="ri-add-line"></i> Nouvel achat
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Montant total</th>
                    <th>Payé</th>
                    <th>Reste</th>
                    <th>Mode</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseur->achats as $achat)
                    <tr style="{{ $achat->statut === 'brouillon' ? 'background:#FFFBEB;' : '' }}">
                        <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;">
                            {{ $achat->date_achat->format('d/m/Y') }}
                        </td>
                        <td style="color:var(--noir-mid);font-size:13px;">{{ $achat->reference ?? '—' }}</td>
                        <td>
                            <span class="montant" style="font-size:14px;font-weight:700;color:var(--noir-text);">
                                {{ number_format($achat->montant_total) }}
                            </span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td>
                            <span class="montant" style="font-size:13px;color:var(--succes);">{{ number_format($achat->montant_paye) }}</span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td>
                            <span class="montant" style="font-size:13px;font-weight:700;color:{{ $achat->montant_reste > 0 ? 'var(--danger)' : 'var(--succes)' }};">
                                {{ number_format($achat->montant_reste) }}
                            </span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td><span class="badge badge-gray">{{ ucfirst(str_replace('_',' ',$achat->mode_paiement)) }}</span></td>
                        <td>
                            @if($achat->statut === 'brouillon')
                                <span class="badge badge-brouillon"><i class="ri-draft-line"></i> Brouillon</span>
                            @elseif($achat->statut === 'solde')
                                <span class="badge badge-green"><i class="ri-check-double-line"></i> Soldé</span>
                            @elseif($achat->statut === 'partiellement_paye')
                                <span class="badge badge-orange"><i class="ri-loader-line"></i> Partiel</span>
                            @elseif($achat->statut === 'valide')
                                <span class="badge {{ $achat->isEcheanceDepassee() ? 'badge-red' : 'badge-blue' }}">
                                    {{ $achat->isEcheanceDepassee() ? '⚠️ Échu' : 'Non payé' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('fournisseurs.achat.show', [$fournisseur, $achat]) }}" class="btn btn-outline btn-sm">
                                <i class="ri-eye-line"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:24px;color:var(--noir-light);">Aucun achat enregistré</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Dépenses liées --}}
<div class="card">
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon green"><i class="ri-receipt-line"></i></div>
            <div>
                <div class="section-title-text">Dépenses liées</div>
                <div class="section-title-sub">{{ $depenses->count() }} dépense(s)</div>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Date</th><th>Libellé</th><th>Catégorie</th><th>Montant</th><th>Source</th></tr>
            </thead>
            <tbody>
                @forelse($depenses as $dep)
                    <tr>
                        <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;">{{ $dep->date_depense->format('d/m/Y') }}</td>
                        <td style="font-weight:600;color:var(--noir-text);">{{ $dep->libelle }}</td>
                        <td>
                            @if($dep->categorie)
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;">
                                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $dep->categorie->couleur }};"></span>
                                    {{ $dep->categorie->nom }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="montant" style="font-size:14px;font-weight:700;color:var(--noir-text);">{{ number_format($dep->montant) }}</span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td>
                            @if($dep->source_type === 'App\Models\Achat')
                                <span class="badge badge-blue"><i class="ri-shopping-bag-line"></i> Achat auto</span>
                            @elseif($dep->source_type === 'App\Models\ReglementFournisseur')
                                <span class="badge badge-green"><i class="ri-check-line"></i> Règlement</span>
                            @else
                                <span class="badge badge-gray"><i class="ri-link"></i> Manuel</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:var(--noir-light);">Aucune dépense liée</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
