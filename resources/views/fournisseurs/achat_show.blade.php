@extends('layouts.app')
@section('title', 'Achat #' . $achat->id)
@section('page-title', 'Achat #' . $achat->id)
@section('page-subtitle', $fournisseur->nom . ' — ' . $achat->date_achat->format('d/m/Y'))

@section('content')

<div class="page-header">
    <div>
        <h2>Achat #{{ $achat->id }}</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">
            {{ $fournisseur->nom }}
            @if($achat->reference) · Réf: {{ $achat->reference }} @endif
        </p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
        @if($achat->statut === 'brouillon')
            <form method="POST" action="{{ route('fournisseurs.achat.valider', [$fournisseur, $achat]) }}"
                  onsubmit="return confirm('Valider cet achat ? Le stock sera mis à jour et une dépense sera créée.')">
                @csrf
                <button class="btn btn-primary btn-lg">
                    <i class="ri-check-double-line"></i> Valider l'achat
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Statut --}}
<div style="margin-bottom:22px;">
    @if($achat->statut === 'brouillon')
        <div class="alert alert-warning">
            <i class="ri-draft-line"></i>
            Cet achat est en <strong>brouillon</strong> — le stock n'a pas encore été mis à jour. Cliquez sur <strong>Valider l'achat</strong> pour confirmer.
        </div>
    @elseif($achat->statut === 'valide')
        <div class="alert" style="background:var(--info-bg);border-color:rgba(30,111,168,.2);color:var(--info);">
            <i class="ri-check-line"></i>
            Achat validé — stock mis à jour. <strong>En attente de règlement.</strong>
            @if($achat->isEcheanceDepassee()) <span style="color:var(--danger);"> ⚠️ Échéance dépassée !</span> @endif
        </div>
    @elseif($achat->statut === 'partiellement_paye')
        <div class="alert alert-warning">
            <i class="ri-loader-line"></i>
            Partiellement réglé — reste <strong>{{ number_format($achat->montant_reste) }} FCFA</strong> à payer.
        </div>
    @elseif($achat->statut === 'solde')
        <div class="alert alert-success">
            <i class="ri-check-double-line"></i> Achat entièrement soldé.
        </div>
    @endif
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    <div class="card">
        <div class="card-header"><span class="card-title">Détails</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div>
                    <div class="form-label">Fournisseur</div>
                    <div style="font-weight:600;color:var(--noir-text);">{{ $fournisseur->nom }}</div>
                </div>
                <div>
                    <div class="form-label">Date</div>
                    <div style="font-family:'DM Mono',monospace;">{{ $achat->date_achat->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div class="form-label">Mode paiement</div>
                    <span class="badge badge-gray">{{ ucfirst(str_replace('_',' ',$achat->mode_paiement)) }}</span>
                </div>
                @if($achat->date_echeance)
                <div>
                    <div class="form-label">Échéance</div>
                    <div style="font-family:'DM Mono',monospace;color:{{ $achat->isEcheanceDepassee() ? 'var(--danger)' : 'var(--noir-text)' }};">
                        {{ $achat->date_echeance->format('d/m/Y') }}
                        @if($achat->isEcheanceDepassee()) <span style="font-size:11px;">⚠️ Dépassée</span> @endif
                    </div>
                </div>
                @endif
                <div>
                    <div class="form-label">Montant total</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;color:var(--noir-text);">
                        {{ number_format($achat->montant_total) }}
                        <span style="font-size:13px;color:var(--noir-light);">FCFA</span>
                    </div>
                </div>
                <div>
                    <div class="form-label">Reste à payer</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:28px;font-weight:600;
                        color:{{ $achat->montant_reste > 0 ? 'var(--danger)' : 'var(--succes)' }};">
                        {{ number_format($achat->montant_reste) }}
                        <span style="font-size:13px;color:var(--noir-light);">FCFA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Lignes de matières</span></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Matière</th><th>Qté</th><th>Prix unit.</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @foreach($achat->lignes as $ligne)
                        <tr>
                            <td style="font-weight:500;color:var(--noir-text);">{{ $ligne->matierePremiere->nom }}</td>
                            <td style="font-family:'DM Mono',monospace;">{{ $ligne->quantite }} {{ $ligne->matierePremiere->unite }}</td>
                            <td style="font-family:'DM Mono',monospace;color:var(--noir-mid);">{{ number_format($ligne->prix_unitaire) }}</td>
                            <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--noir-text);">{{ number_format($ligne->montant) }}</td>
                        </tr>
                    @endforeach
                    <tr style="background:var(--bg-surface);">
                        <td colspan="3" style="font-weight:700;color:var(--noir-text);padding:12px 16px;">Total</td>
                        <td style="font-family:'DM Mono',monospace;font-weight:700;color:var(--noir-text);padding:12px 16px;">
                            {{ number_format($achat->montant_total) }} FCFA
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Règlements --}}
@if($achat->statut !== 'brouillon' && $achat->statut !== 'solde')
<div class="card" style="margin-bottom:22px;border-color:rgba(26,138,74,.25);">
    <div class="card-header" style="background:var(--succes-bg);">
        <span class="card-title" style="color:var(--succes);">Effectuer un règlement</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('fournisseurs.reglement.store', $fournisseur) }}">
            @csrf
            <input type="hidden" name="achat_id" value="{{ $achat->id }}">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Montant (FCFA) *</label>
                    <input type="number" name="montant" class="form-control"
                           value="{{ $achat->montant_reste }}" min="1" max="{{ $achat->montant_reste }}" required>
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
                    <input type="text" name="reference_mobile" class="form-control" placeholder="Optionnel" maxlength="20">
                </div>
            </div>
            <button type="submit" class="btn btn-success">
                <i class="ri-check-double-line"></i> Enregistrer le règlement
            </button>
        </form>
    </div>
</div>
@endif

@if($achat->reglements->count())
<div class="card">
    <div class="card-header"><span class="card-title">Historique des règlements</span></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Montant</th><th>Mode</th><th>Référence</th></tr></thead>
            <tbody>
                @foreach($achat->reglements as $reg)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;font-size:13px;">{{ $reg->date_reglement->format('d/m/Y') }}</td>
                        <td style="font-family:'DM Mono',monospace;font-weight:600;color:var(--succes);">{{ number_format($reg->montant) }} FCFA</td>
                        <td><span class="badge badge-gray">{{ $reg->mode_libelle }}</span></td>
                        <td style="font-family:'DM Mono',monospace;font-size:12px;color:var(--noir-light);">
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
