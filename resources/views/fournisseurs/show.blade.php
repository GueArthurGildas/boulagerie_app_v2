@extends('layouts.app')
@section('title', $fournisseur->nom)
@section('page-title', $fournisseur->nom)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $fournisseur->nom }}</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">{{ ucfirst($fournisseur->type) }} · {{ $fournisseur->ville ?? 'Ville non renseignée' }}</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-outline">
            <i class="ri-pencil-line"></i> Modifier
        </a>
        <a href="{{ route('fournisseurs.achats', $fournisseur) }}" class="btn btn-primary">
            <i class="ri-shopping-bag-line"></i> Nouvel achat
        </a>
    </div>
</div>

{{-- KPI fournisseur --}}
<div class="kpi-grid" style="margin-bottom:24px;">
    <div class="kpi-card">
        <div class="kpi-label">Total achats</div>
        <div class="kpi-value" style="font-size:28px;">{{ number_format($totalAchats) }}</div>
        <div class="kpi-sub">FCFA cumulés</div>
        <i class="ri-shopping-bag-line kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Total réglé</div>
        <div class="kpi-value" style="font-size:28px;">{{ number_format($totalReglements) }}</div>
        <div class="kpi-sub">FCFA payés</div>
        <i class="ri-check-double-line kpi-icon"></i>
    </div>
    <div class="kpi-card {{ $fournisseur->solde_du > 0 ? 'danger' : 'green' }}">
        <div class="kpi-label">Solde dû</div>
        <div class="kpi-value" style="font-size:28px;">{{ number_format($fournisseur->solde_du) }}</div>
        <div class="kpi-sub">FCFA restants</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    @if($fournisseur->plafond_credit > 0)
    <div class="kpi-card">
        <div class="kpi-label">Crédit disponible</div>
        <div class="kpi-value" style="font-size:28px;">{{ number_format($fournisseur->credit_disponible) }}</div>
        <div class="kpi-sub">sur {{ number_format($fournisseur->plafond_credit) }} FCFA</div>
        <i class="ri-bank-line kpi-icon"></i>
    </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    {{-- Infos contact --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Coordonnées</span></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                @if($fournisseur->telephone)
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:var(--bg-surface);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-phone-line" style="color:var(--succes);"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--noir-light);">Téléphone</div>
                        <div style="font-weight:600;color:var(--noir-text);">{{ $fournisseur->telephone }}</div>
                    </div>
                </div>
                @endif
                @if($fournisseur->email)
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:var(--bg-surface);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-mail-line" style="color:var(--info);"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--noir-light);">Email</div>
                        <div style="font-weight:600;color:var(--noir-text);">{{ $fournisseur->email }}</div>
                    </div>
                </div>
                @endif
                @if($fournisseur->contact_nom)
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:var(--bg-surface);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-user-line" style="color:var(--or);"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--noir-light);">Contact</div>
                        <div style="font-weight:600;color:var(--noir-text);">{{ $fournisseur->contact_nom }}</div>
                    </div>
                </div>
                @endif
                @if($fournisseur->adresse || $fournisseur->ville)
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;background:var(--bg-surface);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="ri-map-pin-line" style="color:var(--danger);"></i>
                    </div>
                    <div>
                        <div style="font-size:11px;color:var(--noir-light);">Adresse</div>
                        <div style="font-weight:500;color:var(--noir-text);">
                            {{ $fournisseur->adresse }} {{ $fournisseur->ville ? '— ' . $fournisseur->ville : '' }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @if($fournisseur->notes)
                <hr class="divider">
                <div class="form-label">Notes</div>
                <p style="font-size:13px;color:var(--noir-mid);line-height:1.7;">{{ $fournisseur->notes }}</p>
            @endif
        </div>
    </div>

    {{-- Règlement rapide --}}
    @if($fournisseur->solde_du > 0)
    <div class="card" style="border-color:rgba(192,57,43,.25);">
        <div class="card-header" style="background:var(--danger-bg);">
            <span class="card-title" style="color:var(--danger);">
                <i class="ri-money-dollar-circle-line"></i> Effectuer un règlement
            </span>
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
                                {{ $a->date_achat->format('d/m/Y') }} — {{ number_format($a->montant_reste) }} FCFA restants
                                @if($a->reference) ({{ $a->reference }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Montant (FCFA) *</label>
                        <input type="number" name="montant" class="form-control"
                               value="{{ $fournisseur->solde_du }}" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="date_reglement" class="form-control"
                               value="{{ now()->format('Y-m-d') }}" required>
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
    {{-- Lier une dépense manuellement --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Lier une dépense existante</span>
        </div>
        <div class="card-body">
            <p style="font-size:13px;color:var(--noir-mid);margin-bottom:16px;">
                Associer manuellement une dépense déjà enregistrée à ce fournisseur.
            </p>
            <form method="POST" action="{{ route('fournisseurs.lier-depense', $fournisseur) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Dépense à lier</label>
                    <select name="depense_id" class="form-control" required>
                        <option value="">— Choisir une dépense —</option>
                        @foreach(\App\Models\Depense::whereNull('fournisseur_id')->orderByDesc('date_depense')->limit(50)->get() as $d)
                            <option value="{{ $d->id }}">
                                {{ $d->date_depense->format('d/m/Y') }} — {{ $d->libelle }} ({{ number_format($d->montant) }} FCFA)
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-outline" style="width:100%;">
                    <i class="ri-link"></i> Lier cette dépense
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

{{-- Derniers achats --}}
<div class="card" style="margin-bottom:22px;">
    <div class="card-header">
        <span class="card-title">Derniers achats</span>
        <a href="{{ route('fournisseurs.achats', $fournisseur) }}" class="btn btn-outline btn-sm">
            <i class="ri-add-line"></i> Nouvel achat
        </a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Montant</th>
                    <th>Payé</th>
                    <th>Reste</th>
                    <th>Mode</th>
                    <th>Statut</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($fournisseur->achats as $achat)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:13px;">{{ $achat->date_achat->format('d/m/Y') }}</td>
                        <td style="color:var(--noir-mid);font-size:13px;">{{ $achat->reference ?? '—' }}</td>
                        <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--noir-text);">
                            {{ number_format($achat->montant_total) }}
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--succes);">{{ number_format($achat->montant_paye) }}</td>
                        <td style="font-family:'DM Mono',monospace;color:{{ $achat->montant_reste > 0 ? 'var(--danger)' : 'var(--succes)' }};">
                            {{ number_format($achat->montant_reste) }}
                        </td>
                        <td><span class="badge badge-gray">{{ ucfirst(str_replace('_',' ',$achat->mode_paiement)) }}</span></td>
                        <td>
                            @if($achat->statut === 'solde')
                                <span class="badge badge-green">Soldé</span>
                            @elseif($achat->statut === 'partiellement_paye')
                                <span class="badge badge-orange">Partiel</span>
                            @elseif($achat->statut === 'valide')
                                <span class="badge badge-red {{ $achat->isEcheanceDepassee() ? '' : '' }}">
                                    {{ $achat->isEcheanceDepassee() ? '⚠️ Échu' : 'Non payé' }}
                                </span>
                            @else
                                <span class="badge badge-gray">Brouillon</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('fournisseurs.achat.show', [$fournisseur, $achat]) }}"
                               class="btn btn-outline btn-sm"><i class="ri-eye-line"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:24px;color:var(--noir-light);">
                            Aucun achat enregistré
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Dépenses liées --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Dépenses liées</span>
        <span class="badge badge-gray">{{ $depenses->count() }}</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Montant</th>
                    <th>Source</th>
                </tr>
            </thead>
            <tbody>
                @forelse($depenses as $dep)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:13px;">{{ $dep->date_depense->format('d/m/Y') }}</td>
                        <td style="font-weight:500;color:var(--noir-text);">{{ $dep->libelle }}</td>
                        <td>
                            @if($dep->categorie)
                                <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;">
                                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $dep->categorie->couleur }};"></span>
                                    {{ $dep->categorie->nom }}
                                </span>
                            @endif
                        </td>
                        <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--noir-text);">
                            {{ number_format($dep->montant) }} FCFA
                        </td>
                        <td>
                            @if($dep->source_type === 'App\Models\Achat')
                                <span class="badge badge-blue"><i class="ri-shopping-bag-line"></i> Achat auto</span>
                            @elseif($dep->source_type === 'App\Models\ReglementFournisseur')
                                <span class="badge badge-green"><i class="ri-check-line"></i> Règlement auto</span>
                            @else
                                <span class="badge badge-gray"><i class="ri-link"></i> Manuel</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center;padding:24px;color:var(--noir-light);">
                            Aucune dépense liée à ce fournisseur
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
