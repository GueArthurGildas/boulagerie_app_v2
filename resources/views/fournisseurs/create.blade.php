@extends('layouts.app')
@section('title', isset($fournisseur) ? 'Modifier fournisseur' : 'Nouveau fournisseur')
@section('page-title', isset($fournisseur) ? 'Modifier le fournisseur' : 'Nouveau fournisseur')

@section('content')
<div class="page-header">
    <div>
        <h2>{{ isset($fournisseur) ? $fournisseur->nom : 'Nouveau Fournisseur' }}</h2>
        <div class="or-line"></div>
    </div>
    <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

<div style="max-width:800px;">
    <form method="POST" action="{{ isset($fournisseur) ? route('fournisseurs.update', $fournisseur) : route('fournisseurs.store') }}">
        @csrf
        @if(isset($fournisseur)) @method('PUT') @endif

        <div class="card" style="margin-bottom:20px;">
            <div class="card-header"><span class="card-title">Informations générales</span></div>
            <div class="card-body">
                <div class="form-grid">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nom du fournisseur *</label>
                        <input type="text" name="nom" class="form-control"
                               value="{{ old('nom', $fournisseur->nom ?? '') }}"
                               placeholder="Ex: Minoterie Abidjan, SIFCA..." required>
                        @error('nom')<div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="telephone" class="form-control"
                               value="{{ old('telephone', $fournisseur->telephone ?? '') }}"
                               placeholder="07 00 00 00 00">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $fournisseur->email ?? '') }}"
                               placeholder="contact@fournisseur.ci">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ville</label>
                        <input type="text" name="ville" class="form-control"
                               value="{{ old('ville', $fournisseur->ville ?? '') }}"
                               placeholder="Abidjan, Bouaké...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nom du contact</label>
                        <input type="text" name="contact_nom" class="form-control"
                               value="{{ old('contact_nom', $fournisseur->contact_nom ?? '') }}"
                               placeholder="Personne à contacter">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type de fournisseur</label>
                        <select name="type" class="form-control">
                            @foreach(['general'=>'Général','matiere'=>'Matières premières','emballage'=>'Emballages','equipement'=>'Équipement','service'=>'Service'] as $val => $label)
                                <option value="{{ $val }}" {{ old('type', $fournisseur->type ?? 'general') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Plafond de crédit (FCFA)</label>
                        <input type="number" name="plafond_credit" class="form-control"
                               value="{{ old('plafond_credit', $fournisseur->plafond_credit ?? 0) }}"
                               min="0" step="1000" placeholder="0 = pas de crédit">
                        <div style="font-size:11px;color:var(--noir-light);margin-top:4px;">
                            <i class="ri-information-line"></i> 0 = paiement comptant uniquement
                        </div>
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="adresse" class="form-control"
                               value="{{ old('adresse', $fournisseur->adresse ?? '') }}"
                               placeholder="Adresse complète">
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Notes internes</label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Conditions particulières, remarques...">{{ old('notes', $fournisseur->notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:12px;">
            <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline btn-lg">Annuler</a>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="ri-save-line"></i>
                {{ isset($fournisseur) ? 'Mettre à jour' : 'Créer le fournisseur' }}
            </button>
        </div>
    </form>
</div>
@endsection
