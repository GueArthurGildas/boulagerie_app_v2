@extends('layouts.app')

@section('title', 'Produits')
@section('page-title', 'Produits')
@section('page-subtitle', 'Catalogue des produits finis')

@section('content')

<div class="page-header">
    <div>
        <h2>Produits</h2>
        <p>{{ $produits->total() }} produit(s) enregistré(s)</p>
    </div>
    <a href="{{ route('produits.create') }}" class="btn btn-primary btn-lg">
        <i class="ri-add-circle-line"></i> Nouveau produit
    </a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix de vente</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produits as $p)
                    <tr>
                        <td style="font-family:'DM Mono',monospace;color:var(--gris-mid);font-size:12px;">#{{ $p->id }}</td>
                        <td style="font-weight:600;color:var(--blanc);">{{ $p->nom }}</td>
                        <td>
                            @if($p->categorie)
                                <span class="badge badge-blue">{{ $p->categorie }}</span>
                            @else
                                <span style="color:var(--gris-dark)">—</span>
                            @endif
                        </td>
                        <td style="font-family:'DM Mono',monospace;color:var(--blanc);">
                            {{ number_format($p->prix_vente) }} FCFA
                        </td>
                        <td>
                            <span class="badge {{ $p->actif ? 'badge-green' : 'badge-gray' }}">
                                {{ $p->actif ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('produits.edit', $p) }}" class="btn btn-outline btn-sm">
                                    <i class="ri-pencil-line"></i>
                                </a>
                                <form method="POST" action="{{ route('produits.destroy', $p) }}"
                                      onsubmit="return confirm('Supprimer ce produit ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:48px;color:var(--gris-dark);">
                            <i class="ri-store-2-line" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            Aucun produit enregistré
                            <br>
                            <a href="{{ route('produits.create') }}" class="btn btn-primary" style="margin-top:16px;">
                                <i class="ri-add-line"></i> Créer un produit
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($produits->hasPages())
        <div style="padding:16px 24px;border-top:1px solid var(--noir-border);">
            {{ $produits->links() }}
        </div>
    @endif
</div>

@endsection
