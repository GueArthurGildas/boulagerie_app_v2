@extends('layouts.app')

@section('title', 'Dépense #' . $depense->id)
@section('page-title', 'Dépense #' . $depense->id)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $depense->libelle }}</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">{{ $depense->date_depense->format('d/m/Y') }} — {{ $depense->categorie->nom }}</p>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <a href="{{ route('depenses.index') }}" class="btn btn-outline">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
        @if($depense->statut === 'brouillon')
            <a href="{{ route('depenses.edit', $depense) }}" class="btn btn-outline">
                <i class="ri-pencil-line"></i> Modifier
            </a>
            <form method="POST" action="{{ route('depenses.valider', $depense) }}">
                @csrf
                <button class="btn btn-success"><i class="ri-check-double-line"></i> Valider</button>
            </form>
            <form method="POST" action="{{ route('depenses.rejeter', $depense) }}">
                @csrf
                <button class="btn btn-danger"><i class="ri-close-line"></i> Rejeter</button>
            </form>
        @endif
        @if($depense->est_recurrente)
            <form method="POST" action="{{ route('depenses.cloner', $depense) }}">
                @csrf
                <button class="btn btn-outline" style="color:var(--or);border-color:var(--or-border);">
                    <i class="ri-file-copy-line"></i> Cloner
                </button>
            </form>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">

    <div class="card">
        <div class="card-header">
            <span class="card-title">Détails</span>
            @if($depense->statut === 'validee')
                <span class="badge badge-green"><i class="ri-check-line"></i> Validée</span>
            @elseif($depense->statut === 'brouillon')
                <span class="badge badge-gray"><i class="ri-draft-line"></i> Brouillon</span>
            @else
                <span class="badge badge-red"><i class="ri-close-line"></i> Rejetée</span>
            @endif
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
                <div>
                    <div class="form-label">Montant</div>
                    <div style="font-family:'Cormorant Garamond',serif;font-size:36px;font-weight:600;color:var(--noir-text);">
                        {{ number_format($depense->montant) }}
                    </div>
                    <div style="font-size:12px;color:var(--noir-light);margin-top:2px;">FCFA</div>
                </div>
                <div>
                    <div class="form-label">Catégorie</div>
                    <div style="display:flex;align-items:center;gap:7px;margin-top:4px;">
                        <span style="width:10px;height:10px;border-radius:50%;background:{{ $depense->categorie->couleur }};"></span>
                        <span style="font-weight:600;color:var(--noir-text);">{{ $depense->categorie->nom }}</span>
                    </div>
                </div>
                <div>
                    <div class="form-label">Date</div>
                    <div style="font-family:'DM Mono',monospace;font-weight:500;color:var(--noir-text);">
                        {{ $depense->date_depense->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <div class="form-label">Mode de paiement</div>
                    <span class="badge badge-gray" style="margin-top:4px;">{{ $depense->mode_libelle }}</span>
                </div>
                @if($depense->beneficiaire)
                    <div>
                        <div class="form-label">Bénéficiaire</div>
                        <div style="font-weight:500;color:var(--noir-text);">{{ $depense->beneficiaire }}</div>
                    </div>
                @endif
                @if($depense->reference_mobile)
                    <div>
                        <div class="form-label">Réf. transaction</div>
                        <div style="font-family:'DM Mono',monospace;font-size:13px;color:var(--noir-mid);">
                            {{ $depense->reference_mobile }}
                        </div>
                    </div>
                @endif
            </div>

            @if($depense->notes)
                <hr class="divider">
                <div class="form-label">Notes</div>
                <p style="font-size:13px;color:var(--noir-mid);line-height:1.7;">{{ $depense->notes }}</p>
            @endif
        </div>
    </div>

    <div>
        @if($depense->est_recurrente)
        <div class="card" style="margin-bottom:18px;border-color:var(--or-border);">
            <div class="card-header" style="background:var(--or-pale);">
                <span class="card-title" style="color:var(--or);">
                    <i class="ri-repeat-line"></i> Dépense récurrente
                </span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--noir-mid);margin-bottom:14px;">
                    Cette dépense est marquée comme modèle récurrent
                    @if($depense->frequence_recurrence)
                        ({{ $depense->frequence_recurrence }}).
                    @endif
                    Cliquez sur <strong>Cloner</strong> pour créer une nouvelle dépense basée sur ce modèle.
                </p>
                <form method="POST" action="{{ route('depenses.cloner', $depense) }}">
                    @csrf
                    <button class="btn btn-gold">
                        <i class="ri-file-copy-line"></i> Cloner cette dépense
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header">
                <span class="card-title">Traçabilité</span>
            </div>
            <div class="card-body">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:32px;height:32px;background:var(--bg-surface);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ri-add-circle-line" style="color:var(--noir-light);"></i>
                        </div>
                        <div>
                            <div style="font-size:12px;font-weight:600;color:var(--noir-text);">Créée par</div>
                            <div style="font-size:13px;color:var(--noir-mid);">
                                {{ $depense->createdBy->name ?? '—' }}
                                <span style="color:var(--noir-light);"> · {{ $depense->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($depense->statut === 'validee' && $depense->validePar)
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;background:var(--succes-bg);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="ri-check-double-line" style="color:var(--succes);"></i>
                            </div>
                            <div>
                                <div style="font-size:12px;font-weight:600;color:var(--noir-text);">Validée par</div>
                                <div style="font-size:13px;color:var(--noir-mid);">
                                    {{ $depense->validePar->name }}
                                    <span style="color:var(--noir-light);"> · {{ $depense->valide_le->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
