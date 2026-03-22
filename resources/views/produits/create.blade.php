@extends('layouts.app')

@section('title', isset($produit) ? 'Modifier produit' : 'Nouveau produit')
@section('page-title', isset($produit) ? 'Modifier le produit' : 'Nouveau produit')

@section('content')

<div class="page-header">
    <div>
        <h2>{{ isset($produit) ? 'Modifier : ' . $produit->nom : 'Nouveau Produit' }}</h2>
    </div>
    <a href="{{ route('produits.index') }}" class="btn btn-outline">
        <i class="ri-arrow-left-line"></i> Retour
    </a>
</div>

<div style="max-width:600px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Informations du produit</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ isset($produit) ? route('produits.update', $produit) : route('produits.store') }}">
                @csrf
                @if(isset($produit)) @method('PUT') @endif

                <div class="form-group">
                    <label class="form-label">Nom du produit *</label>
                    <input type="text" name="nom" class="form-control"
                           value="{{ old('nom', $produit->nom ?? '') }}"
                           placeholder="Ex: Pain baguette 250g" required>
                    @error('nom')
                        <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie" class="form-control">
                            <option value="">— Aucune —</option>
                            @foreach(['Pain','Viennoiserie','Gâteau','Pâtisserie','Autre'] as $cat)
                                <option value="{{ $cat }}" {{ old('categorie', $produit->categorie ?? '') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prix de vente (FCFA) *</label>
                        <input type="number" name="prix_vente" class="form-control"
                               step="1" min="0"
                               value="{{ old('prix_vente', $produit->prix_vente ?? 0) }}" required>
                        @error('prix_vente')
                            <div class="form-error"><i class="ri-error-warning-line"></i> {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(isset($produit))
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <div style="display:flex;gap:16px;margin-top:4px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="actif" value="1" {{ old('actif', $produit->actif) ? 'checked' : '' }}>
                            <span style="font-size:14px;color:var(--blanc);">Actif</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="actif" value="0" {{ !old('actif', $produit->actif) ? 'checked' : '' }}>
                            <span style="font-size:14px;color:var(--gris-light);">Inactif</span>
                        </label>
                    </div>
                </div>
                @endif

                <div style="display:flex;justify-content:flex-end;gap:12px;margin-top:8px;">
                    <a href="{{ route('produits.index') }}" class="btn btn-outline btn-lg">Annuler</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ri-save-line"></i>
                        {{ isset($produit) ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
