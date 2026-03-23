@extends('layouts.app')
@section('title', 'Achat #' . $achat->id)
@section('page-title', 'Achat #' . $achat->id)
@section('page-subtitle', $fournisseur->nom . ' — ' . $achat->date_achat->format('d/m/Y'))

@section('content')

<div class="page-header">
    <div class="page-header-left">
        <h2>Achat #{{ $achat->id }}</h2>
        <div class="title-bar">
            <div class="title-bar-line"></div>
            <span class="title-bar-text">
                {{ $fournisseur->nom }}
                @if($achat->reference) · Réf : {{ $achat->reference }} @endif
            </span>
        </div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-back">
            <i class="ri-arrow-left-line"></i> Retour
        </a>

        @if($achat->statut === 'brouillon')
            {{-- Annuler le brouillon --}}
            <form method="POST" action="{{ route('fournisseurs.achat.annuler', [$fournisseur, $achat]) }}"
                  onsubmit="return confirm('Annuler cet achat ? Il sera supprimé définitivement.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger">
                    <i class="ri-delete-bin-line"></i> Annuler l'achat
                </button>
            </form>

            {{-- Valider --}}
            <form method="POST" action="{{ route('fournisseurs.achat.valider', [$fournisseur, $achat]) }}"
                  onsubmit="return confirm('Valider ? Le stock sera mis à jour et une dépense sera créée.')">
                @csrf
                <button class="btn btn-primary btn-lg">
                    <i class="ri-check-double-line"></i> Valider l'achat
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Banner statut --}}
@if($achat->statut === 'brouillon')
    <div style="margin-bottom:20px;padding:14px 18px;background:#FFF3CD;border:1.5px solid #FFD452;border-radius:10px;display:flex;align-items:center;gap:12px;">
        <div style="width:36px;height:36px;background:#FFD452;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ri-draft-line" style="font-size:18px;color:#856404;"></i>
        </div>
        <div>
            <div style="font-weight:700;color:#856404;font-size:14px;">Achat en brouillon</div>
            <div style="font-size:12px;color:#A07800;margin-top:2px;">
                Le stock n'a pas encore été mis à jour. Validez pour confirmer l'achat,
                ou annulez pour le supprimer.
            </div>
        </div>
    </div>
@elseif($achat->statut === 'valide')
    <div class="alert alert-info" style="margin-bottom:20px;">
        <i class="ri-check-line"></i>
        Achat validé — stock mis à jour.
        <strong>En attente de règlement.</strong>
        @if($achat->isEcheanceDepassee())
            <span style="color:var(--danger);margin-left:8px;"> ⚠️ Échéance dépassée !</span>
        @endif
    </div>
@elseif($achat->statut === 'partiellement_paye')
    <div class="alert alert-warning" style="margin-bottom:20px;">
        <i class="ri-loader-line"></i>
        Partiellement réglé — reste
        <strong class="montant" style="margin:0 4px;">{{ number_format($achat->montant_reste) }}</strong> FCFA à payer.
    </div>
@elseif($achat->statut === 'solde')
    <div class="alert alert-success" style="margin-bottom:20px;">
        <i class="ri-check-double-line"></i> Achat entièrement soldé.
    </div>
@endif

{{-- KPI achat --}}
<div class="kpi-grid" style="margin-bottom:22px;">
    <div class="kpi-card">
        <div class="kpi-label">Montant total</div>
        <div class="kpi-value-currency">
            {{ number_format($achat->montant_total) }}<span class="montant-unit">FCFA</span>
        </div>
        <div class="kpi-sub">{{ $achat->lignes->count() }} ligne(s)</div>
        <i class="ri-shopping-bag-line kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Montant payé</div>
        <div class="kpi-value-currency" style="color:var(--succes);">
            {{ number_format($achat->montant_paye) }}<span class="montant-unit">FCFA</span>
        </div>
        <i class="ri-check-double-line kpi-icon"></i>
    </div>
    <div class="kpi-card {{ $achat->montant_reste > 0 ? 'danger' : 'green' }}">
        <div class="kpi-label">Reste à payer</div>
        <div class="kpi-value-currency"
             style="color:{{ $achat->montant_reste > 0 ? 'var(--danger)' : 'var(--succes)' }};">
            {{ number_format($achat->montant_reste) }}<span class="montant-unit">FCFA</span>
        </div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">Mode paiement</div>
        <div style="margin-top:8px;">
            <span class="badge badge-gray" style="font-size:13px;padding:6px 12px;">
                {{ ucfirst(str_replace('_', ' ', $achat->mode_paiement)) }}
            </span>
        </div>
        @if($achat->date_echeance)
            <div class="kpi-sub" style="color:{{ $achat->isEcheanceDepassee() ? 'var(--danger)' : 'var(--noir-light)' }};">
                Échéance : {{ $achat->date_echeance->format('d/m/Y') }}
            </div>
        @endif
    </div>
</div>

{{-- Détails + lignes --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    <div class="card">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon gray"><i class="ri-information-line"></i></div>
                <div><div class="section-title-text">Détails</div></div>
            </div>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <div class="form-label">Fournisseur</div>
                    <div style="font-weight:700;color:var(--noir-text);">{{ $fournisseur->nom }}</div>
                </div>
                <div>
                    <div class="form-label">Date achat</div>
                    <div style="font-family:var(--font-mono);font-weight:600;color:var(--noir-text);">
                        {{ $achat->date_achat->format('d/m/Y') }}
                    </div>
                </div>
                @if($achat->reference)
                <div>
                    <div class="form-label">Référence</div>
                    <div style="font-family:var(--font-mono);font-size:13px;color:var(--noir-mid);">{{ $achat->reference }}</div>
                </div>
                @endif
                @if($achat->date_echeance)
                <div>
                    <div class="form-label">Échéance</div>
                    <div style="font-family:var(--font-mono);font-weight:600;
                        color:{{ $achat->isEcheanceDepassee() ? 'var(--danger)' : 'var(--noir-text)' }};">
                        {{ $achat->date_echeance->format('d/m/Y') }}
                        @if($achat->isEcheanceDepassee())
                            <span class="badge badge-red" style="margin-left:4px;">⚠️ Dépassée</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @if($achat->notes)
                <hr class="divider">
                <div class="form-label">Notes</div>
                <p style="font-size:13px;color:var(--noir-mid);line-height:1.7;">{{ $achat->notes }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="section-header">
            <div class="section-title">
                <div class="section-title-icon gold"><i class="ri-stack-line"></i></div>
                <div>
                    <div class="section-title-text">Lignes de matières</div>
                    <div class="section-title-sub">{{ $achat->lignes->count() }} matière(s) achetée(s)</div>
                </div>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière</th>
                        <th>Quantité</th>
                        <th>Prix unit.</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($achat->lignes as $ligne)
                        <tr>
                            <td style="font-weight:600;color:var(--noir-text);">{{ $ligne->matierePremiere->nom }}</td>
                            <td>
                                <span class="montant" style="font-size:13px;">{{ $ligne->quantite }}</span>
                                <span style="font-size:11px;color:var(--noir-light);"> {{ $ligne->matierePremiere->unite }}</span>
                            </td>
                            <td>
                                <span class="montant" style="font-size:13px;color:var(--noir-mid);">{{ number_format($ligne->prix_unitaire) }}</span>
                                <span class="montant-unit">FCFA</span>
                            </td>
                            <td>
                                <span class="montant" style="font-size:14px;font-weight:700;color:var(--noir-text);">
                                    {{ number_format($ligne->montant) }}
                                </span>
                                <span class="montant-unit">FCFA</span>
                            </td>
                        </tr>
                    @endforeach
                    <tr style="background:var(--bg-surface);">
                        <td colspan="3" style="font-weight:700;color:var(--noir-text);padding:12px 16px;font-size:13px;letter-spacing:.5px;">
                            TOTAL
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="montant-lg" style="color:var(--noir-text);">
                                {{ number_format($achat->montant_total) }}
                            </span>
                            <span style="font-size:12px;font-weight:700;color:var(--noir-light);margin-left:4px;">FCFA</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Règlement si non brouillon et non soldé --}}
@if(!in_array($achat->statut, ['brouillon', 'solde']))
<div class="card" style="margin-bottom:22px;border-color:var(--succes-border);">
    <div class="section-header" style="background:var(--succes-bg);">
        <div class="section-title">
            <div class="section-title-icon green"><i class="ri-money-dollar-circle-line"></i></div>
            <div>
                <div class="section-title-text" style="color:var(--succes);">Effectuer un règlement</div>
                <div class="section-title-sub">
                    Reste dû :
                    <span class="montant" style="color:var(--danger);font-size:13px;">{{ number_format($achat->montant_reste) }}</span> FCFA
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('fournisseurs.reglement.store', $fournisseur) }}">
            @csrf
            <input type="hidden" name="achat_id" value="{{ $achat->id }}">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Montant (FCFA) *</label>
                    <div style="position:relative;">
                        <input type="number" name="montant" class="form-control"
                               value="{{ $achat->montant_reste }}"
                               min="1" max="{{ $achat->montant_reste }}" required
                               style="padding-right:50px;">
                        <span style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                                     font-size:10px;font-weight:700;color:var(--or);pointer-events:none;">FCFA</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Date règlement *</label>
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
                    <input type="text" name="reference_mobile" class="form-control"
                           placeholder="Optionnel" maxlength="20">
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="ri-check-double-line"></i> Enregistrer le règlement
            </button>
        </form>
    </div>
</div>
@endif

{{-- Historique règlements --}}
@if($achat->reglements->count())
<div class="card">
    <div class="section-header">
        <div class="section-title">
            <div class="section-title-icon green"><i class="ri-history-line"></i></div>
            <div>
                <div class="section-title-text">Historique des règlements</div>
                <div class="section-title-sub">{{ $achat->reglements->count() }} règlement(s)</div>
            </div>
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Date</th><th>Montant</th><th>Mode</th><th>Référence</th></tr>
            </thead>
            <tbody>
                @foreach($achat->reglements as $reg)
                    <tr>
                        <td style="font-family:var(--font-mono);font-size:13px;font-weight:500;">
                            {{ $reg->date_reglement->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="montant" style="font-size:14px;font-weight:700;color:var(--succes);">
                                {{ number_format($reg->montant) }}
                            </span>
                            <span class="montant-unit">FCFA</span>
                        </td>
                        <td><span class="badge badge-gray">{{ $reg->mode_libelle }}</span></td>
                        <td style="font-family:var(--font-mono);font-size:12px;color:var(--noir-light);">
                            {{ $reg->reference_mobile ?? $reg->reference_banque ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
