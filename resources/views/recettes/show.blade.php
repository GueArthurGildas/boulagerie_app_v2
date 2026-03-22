@extends('layouts.app')

@section('title', $recette->nom)
@section('page-title', $recette->nom)

@section('content')

<div class="page-header">
    <div>
        <h2>{{ $recette->nom }}</h2>
        <p>Fiche technique — {{ $recette->nb_pieces_attendues }} pièces attendues par fournée</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('recettes.edit', $recette) }}" class="btn btn-outline">
            <i class="ri-pencil-line"></i> Modifier
        </a>
        <a href="{{ route('productions.create') }}?recette_id={{ $recette->id }}" class="btn btn-primary">
            <i class="ri-fire-line"></i> Démarrer une fournée
        </a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">Composition</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Matière première</th>
                        <th>Quantité</th>
                        <th>Stock actuel</th>
                        <th>Disponibilité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recette->lignes as $ligne)
                        @php $ok = $ligne->matierePremiere->stock_actuel >= $ligne->quantite; @endphp
                        <tr>
                            <td style="font-weight:500">{{ $ligne->matierePremiere->nom }}</td>
                            <td style="font-family:'DM Mono',monospace;">
                                {{ $ligne->quantite }} {{ $ligne->matierePremiere->unite }}
                            </td>
                            <td style="font-family:'DM Mono',monospace;color:{{ $ok ? 'var(--succes)' : 'var(--rouge-vif)' }}">
                                {{ $ligne->matierePremiere->stock_actuel }} {{ $ligne->matierePremiere->unite }}
                            </td>
                            <td>
                                <span class="badge {{ $ok ? 'badge-green' : 'badge-red' }}">
                                    <i class="ri-{{ $ok ? 'check' : 'close' }}-line"></i>
                                    {{ $ok ? 'OK' : 'Insuffisant' }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">Statistiques</span>
        </div>
        <div class="card-body">
            @php
                $totalProductions = $recette->productions()->count();
                $rendementMoyen   = $recette->productions()->where('statut','terminee')->avg('rendement') ?? 0;
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div style="text-align:center;padding:20px;background:var(--noir-border);border-radius:10px;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:40px;color:var(--rouge)">{{ $totalProductions }}</div>
                    <div style="font-size:12px;color:var(--gris-mid);text-transform:uppercase;letter-spacing:1px;">Fournées total</div>
                </div>
                <div style="text-align:center;padding:20px;background:var(--noir-border);border-radius:10px;">
                    <div style="font-family:'Bebas Neue',sans-serif;font-size:40px;color:{{ $rendementMoyen >= 90 ? 'var(--succes)' : 'var(--warning)' }}">
                        {{ round($rendementMoyen) }}%
                    </div>
                    <div style="font-size:12px;color:var(--gris-mid);text-transform:uppercase;letter-spacing:1px;">Rendement moyen</div>
                </div>
            </div>
            @if($recette->description)
                <hr class="divider">
                <div class="form-label">Description</div>
                <p style="font-size:14px;color:var(--gris-light);line-height:1.7;">{{ $recette->description }}</p>
            @endif
        </div>
    </div>
</div>

@endsection
