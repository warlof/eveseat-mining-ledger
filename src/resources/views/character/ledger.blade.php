@extends('web::character.layouts.view', ['viewname' => 'mining-ledger'])

@section('title', trans_choice('web::seat.character', 1) . ' ' . trans('mining-ledger::seat.mining'))
@section('page_header', trans_choice('web::seat.character', 1) . ' ' . trans('mining-ledger::seat.mining'))

@inject('request', Illuminate\Http\Request')

@section('character_content')
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left">{{ trans('mining-ledger::seat.mining') }}</h3>
            <div class="pull-right">
                @if(is_null($token))
                <a href="{{ route('auth.mining_ledger', ['character', request()->route()->parameter('character_id')]) }}">Activate</a>
                @else
                <span class="label label-success">Enabled</span>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <table class="table compact table-condensed table-hover table-responsive" id="character-mining-ledger">
                <thead>
                    <tr>
                        <th>{{ trans('web::seat.date') }}</th>
                        <th>{{ trans('web::seat.system') }}</th>
                        <th>{{ trans('mining-ledger::seat.ore') }}</th>
                        <th>{{ trans('web::seat.quantity') }}</th>
                        <th>{{ trans('web::seat.volume') }}</th>
                        <th>{{ trans('web::seat.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledger as $entry)
                    <tr>
                        <td data-order="{{ $entry->date }}">
                            <span data-toggle="tooltip" data-placement="top" title="{{ $entry->date }}">{{ $entry->date }}</span>
                        </td>
                        <td data-order="{{ $entry->system->itemName }}">
                            <a href="//evemaps.dotlan.net/system/{{ $entry->system->itemName }}" target="_blank">
                                <span class="fa fa-map-marker"></span>
                            </a>
                            {{ $entry->system->itemName }}
                        </td>
                        <td data-order="{{ $entry->type->typeName }}">
                            {!! img('type', $entry->type->typeID, 32, ['class' => 'img-circle eve-icon small-icon']) !!}
                            {{ $entry->type->typeName }}
                        </td>
                        <td class="text-right" data-order="{{ $entry->quantity }}">{{ number_format($entry->quantity) }}</td>
                        <td class="text-right" data-order="{{ $entry->volumes }}">{{ number_format($entry->volumes, 2) }} m3</td>
                        @if(is_null($entry->type) || is_null($entry->type->prices))
                        <td class="text-right" data-order="{{ $entry->type->prices }}">0.00 ISK</td>
                        @else
                        <td class="text-right" data-order="{{ $entry->type->prices }}">
                            {{ number_format($entry->quantity * $entry->type->prices->average_price, 2) }} ISK
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('javascript')
    <script type="text/javascript">
        $(function(){
            $('#character-mining-ledger').dataTable({
                'order': [
                    [0, 'desc'],
                    [3, 'desc']
                ]
            });
        });
    </script>
@endpush
