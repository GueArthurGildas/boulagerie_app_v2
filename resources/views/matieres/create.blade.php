@extends('layouts.app')

@section('title', isset($matiere) ? 'Modifier matière' : 'Nouvelle matière')
@section('page-title', isset($matiere) ? 'Modifier la matière' : 'Nouvelle matière première')

@section('content')

<div class="page-header">
    <div>
        <h2>{{ isset($matiere) ? 'Modifier : ' . $matiere->nom : 'Nouvelle Matière Première' }}</h2>
    </div>
    <a href="{{ route('matieres-premieres.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

<div style="max-width:700px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($matiere) ? route('matieres-premieres.update', $matiere) : route('matieres-premieres.store') }}">
                @csrf
                @if(isset($matiere)) @method('PUT') @endif

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom *</label>
                        <input type="text" name="nom" class="form-control"
                               value="{{ old('nom', $matiere->nom ?? '') }}"
                               placeholder="Ex: Farine de blé" required>
                        @error('nom')
                            <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Unité de mesure *</label>
                        <select name="unite" class="form-control" required>
                            <option value="">— Choisir —</option>
                            @foreach(['kg','g','litre','ml','sac','carton','pièce','boîte'] as $u)
                                <option value="{{ $u }}" {{ old('unite', $matiere->unite ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                        @error('unite')
                            <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stock actuel</label>
                        <input type="number" name="stock_actuel" class="form-control"
                               step="0.001" min="0"
                               value="{{ old('stock_actuel', $matiere->stock_actuel ?? 0) }}"
                               {{ isset($matiere) ? 'readonly style=opacity:.5;cursor:not-allowed' : '' }}>
                        @if(isset($matiere))
                            <div style="font-size:11px;color:var(--gris-mid);margin-top:4px;">
                                <i class="ri-information-line"></i> Le stock est mis à jour via les entrées/sorties
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label">Seuil minimum (alerte)</label>
                        <input type="number" name="stock_minimum" class="form-control"
                               step="0.001" min="0"
                               value="{{ old('stock_minimum', $matiere->stock_minimum ?? 0) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prix moyen (FCFA)</label>
                        <input type="number" name="prix_moyen_pondere" class="form-control"
                               step="1" min="0"
                               value="{{ old('prix_moyen_pondere', $matiere->prix_moyen_pondere ?? 0) }}"
                               {{ isset($matiere) ? 'readonly style=opacity:.5;cursor:not-allowed' : '' }}>
                        @if(isset($matiere))
                            <div style="font-size:11px;color:var(--gris-mid);margin-top:4px;">
                                <i class="ri-information-line"></i> Calculé automatiquement (PMP)
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label">Date de péremption</label>
                        <input type="date" name="date_peremption" class="form-control"
                               value="{{ old('date_peremption', isset($matiere) && $matiere->date_peremption ? $matiere->date_peremption->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:8px;">
                    <a href="{{ route('matieres-premieres.index') }}" class="btn btn-outline btn-lg">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ri-save-line"></i>
                        {{ isset($matiere) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
