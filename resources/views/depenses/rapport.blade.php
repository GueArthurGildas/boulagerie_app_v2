@extends('layouts.app')

@section('title', 'Rapport dépenses')
@section('page-title', 'Rapport Dépenses')
@section('page-subtitle', $debut->locale('fr')->isoFormat('MMMM YYYY'))

@push('styles')
<style>
.rapport-bar {
    height: 8px; border-radius: 10px;
    background: var(--noir-border); overflow: hidden; margin-top: 6px;
}
.rapport-bar-fill { height: 100%; border-radius: 10px; transition: width .5s; }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h2>Rapport Dépenses</h2>
        <div class="or-line"></div>
        <p style="margin-top:8px;">{{ $debut->locale('fr')->isoFormat('MMMM YYYY') }}</p>
    </div>
    <div style="display:flex;gap:8px;align-items:flex-end;">
        <form method="GET" style="display:flex;gap:8px;align-items:flex-end;">
            <div>
                <label class="form-label">Période</label>
                <select name="mois" class="form-control" onchange="this.form.submit()">
                    @foreach($moisDisponibles as $m)
                        <option value="{{ $m['mois'] }}" data-annee="{{ $m['annee'] }}"
                            {{ $m['mois'] == $mois && $m['annee'] == $annee ? 'selected' : '' }}>
                            {{ $m['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="annee" id="anneeHidden" value="{{ $annee }}">
        </form>
        <a href="{{ route('depenses.index') }}" class="btn btn-outline">
            <i class="ri-arrow-left-line"></i> Retour
        </a>
    </div>
</div>

{{-- KPI résumé --}}
<div class="kpi-grid" style="margin-bottom:28px;">
    <div class="kpi-card">
        <div class="kpi-label">Total ce mois</div>
        <div class="kpi-value" style="font-size:32px;">{{ number_format($totalMois) }}</div>
        <div class="kpi-sub">FCFA</div>
        <i class="ri-money-dollar-circle-line kpi-icon"></i>
    </div>
    <div class="kpi-card {{ $totalMois > $totalMoisPrecedent ? 'danger' : 'green' }}">
        <div class="kpi-label">Mois précédent</div>
        <div class="kpi-value" style="font-size:32px;">{{ number_format($totalMoisPrecedent) }}</div>
        <div class="kpi-sub">
            @if($totalMoisPrecedent > 0)
                @php $diff = round((($totalMois - $totalMoisPrecedent) / $totalMoisPrecedent) * 100, 1); @endphp
                <span style="color:{{ $diff > 0 ? 'var(--danger)' : 'var(--succes)' }};">
                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}% vs mois précédent
                </span>
            @else
                Pas de données
            @endif
        </div>
        <i class="ri-arrow-up-down-line kpi-icon"></i>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-label">Nb dépenses</div>
        <div class="kpi-value">{{ $parCategorie->sum('nb') }}</div>
        <div class="kpi-sub">ce mois</div>
        <i class="ri-list-check kpi-icon"></i>
    </div>
    <div class="kpi-card green">
        <div class="kpi-label">Moy. par dépense</div>
        <div class="kpi-value" style="font-size:28px;">
            {{ $parCategorie->sum('nb') > 0 ? number_format(intval($totalMois / $parCategorie->sum('nb'))) : 0 }}
        </div>
        <div class="kpi-sub">FCFA</div>
        <i class="ri-bar-chart-line kpi-icon"></i>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:22px;">

    {{-- Par catégorie --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Par catégorie</span>
        </div>
        <div class="card-body">
            @forelse($parCategorie as $item)
                @php $pct = $totalMois > 0 ? ($item->total / $totalMois * 100) : 0; @endphp
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:7px;">
                            <span style="width:9px;height:9px;border-radius:50%;background:{{ $item->categorie->couleur ?? '#888' }};flex-shrink:0;"></span>
                            <span style="font-size:13px;font-weight:600;color:var(--noir-text);">{{ $item->categorie->nom }}</span>
                            <span class="badge badge-gray" style="font-size:10px;">{{ $item->nb }}</span>
                        </div>
                        <div style="text-align:right;">
                            <span style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:var(--noir-text);">
                                {{ number_format($item->total) }}
                            </span>
                            <span style="font-size:11px;color:var(--noir-light);"> FCFA</span>
                        </div>
                    </div>
                    <div class="rapport-bar">
                        <div class="rapport-bar-fill"
                             style="width:{{ round($pct) }}%;background:{{ $item->categorie->couleur ?? 'var(--or)' }};"></div>
                    </div>
                    <div style="font-size:11px;color:var(--noir-light);margin-top:2px;">{{ round($pct, 1) }}%</div>
                </div>
            @empty
                <p style="text-align:center;color:var(--noir-light);padding:24px 0;">Aucune dépense ce mois</p>
            @endforelse
        </div>
    </div>

    {{-- Par mode de paiement --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Par mode de paiement</span>
        </div>
        <div class="card-body">
            @php
                $modeColors = ['cash'=>'var(--succes)','orange_money'=>'#FF6B00','wave'=>'#1A73E8','mtn_momo'=>'#FFCC00','banque'=>'#2C3E50','autre'=>'#888888'];
                $modeLabels = ['cash'=>'Cash','orange_money'=>'Orange Money','wave'=>'Wave','mtn_momo'=>'MTN MoMo','banque'=>'Banque','autre'=>'Autre'];
                $totalMode  = $parMode->sum('total');
            @endphp
            @forelse($parMode as $item)
                @php $pct = $totalMode > 0 ? ($item->total / $totalMode * 100) : 0; @endphp
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:600;color:var(--noir-text);">
                            {{ $modeLabels[$item->mode_paiement] ?? $item->mode_paiement }}
                        </span>
                        <span style="font-family:'DM Mono',monospace;font-size:13px;font-weight:600;color:var(--noir-text);">
                            {{ number_format($item->total) }} FCFA
                        </span>
                    </div>
                    <div class="rapport-bar">
                        <div class="rapport-bar-fill"
                             style="width:{{ round($pct) }}%;background:{{ $modeColors[$item->mode_paiement] ?? '#888' }};"></div>
                    </div>
                    <div style="font-size:11px;color:var(--noir-light);margin-top:2px;">
                        {{ round($pct, 1) }}% · {{ $item->nb }} opération(s)
                    </div>
                </div>
            @empty
                <p style="text-align:center;color:var(--noir-light);padding:24px 0;">Aucune donnée</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Évolution journalière --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Évolution journalière</span>
    </div>
    <div class="card-body">
        @if($parJour->count())
            @php $maxJour = $parJour->max('total'); @endphp
            <div style="display:flex;align-items:flex-end;gap:4px;height:120px;padding-bottom:24px;position:relative;">
                {{-- Ligne de base --}}
                <div style="position:absolute;bottom:24px;left:0;right:0;border-top:1px dashed var(--noir-border);"></div>

                @foreach($parJour as $jour)
                    @php $h = $maxJour > 0 ? round(($jour->total / $maxJour) * 100) : 0; @endphp
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;position:relative;">
                        <div title="{{ number_format($jour->total) }} FCFA le {{ \Carbon\Carbon::parse($jour->jour)->format('d/m') }}"
                             style="width:100%;background:linear-gradient(180deg,var(--or),var(--or-vif));
                                    border-radius:3px 3px 0 0;height:{{ $h }}px;min-height:2px;
                                    transition:opacity .2s;cursor:pointer;"
                             onmouseenter="this.style.opacity='.7'"
                             onmouseleave="this.style.opacity='1'">
                        </div>
                        <span style="font-size:9px;color:var(--noir-light);position:absolute;bottom:0;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($jour->jour)->format('d') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align:center;color:var(--noir-light);padding:24px 0;">Aucune dépense enregistrée ce mois</p>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Sync annee quand on change le mois
document.querySelector('select[name="mois"]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('anneeHidden').value = opt.dataset.annee;
});
</script>
@endpush
