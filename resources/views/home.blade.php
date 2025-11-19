@extends('layouts.app')

@section('content')
    <div id="main-wrapper" class="page-wrapper" style="min-height: 207px;">
        <div class="container-fluid">
            <div class="card mb-3 business-analytics">
                <div class="card-body">
                    <div class="row flex-between align-items-center g-2 mb-3 order_stats_header">
                        <div class="col-sm-6">
                            <h4 class="d-flex align-items-center text-capitalize gap-10 mb-0">
                                {{ trans('lang.dashboard_business_analytics') }}
                            </h4>
                        </div>
                    </div>

                    @unless($vendorExists)
                        <div class="alert alert-warning mb-3">
                            {{ __('Complete your restaurant profile to unlock dashboard insights.') }}
                        </div>
                    @endunless

                    <div class="row business-analytics_list">
                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card-box">
                                <h5>{{ trans('lang.dashboard_total_earnings') }}</h5>
                                <h2>{{ $stats['total_earnings_formatted'] }}</h2>
                                <i class="mdi mdi-cash-usd"></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card-box">
                                <h5>{{ trans('lang.dashboard_total_orders') }}</h5>
                                <h2>{{ $stats['total_orders'] }}</h2>
                                <i class="mdi mdi-cart"></i>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-4 mb-3">
                            <div class="card-box">
                                <h5>{{ trans('lang.dashboard_total_products') }}</h5>
                                <h2>{{ $stats['total_products'] }}</h2>
                                <i class="mdi mdi-buffer"></i>
                            </div>
                        </div>

                        @php
                            $statusCards = [
                                'placed' => ['icon' => 'mdi-lan-pending', 'label' => trans('lang.dashboard_order_placed')],
                                'confirmed' => ['icon' => 'mdi-check-circle', 'label' => trans('lang.dashboard_order_confirmed')],
                                'shipped' => ['icon' => 'mdi-clipboard-outline', 'label' => trans('lang.dashboard_order_shipped')],
                                'completed' => ['icon' => 'mdi-check-circle-outline', 'label' => trans('lang.dashboard_order_completed')],
                                'canceled' => ['icon' => 'mdi-window-close', 'label' => trans('lang.dashboard_order_canceled')],
                                'failed' => ['icon' => 'mdi-alert-circle-outline', 'label' => trans('lang.dashboard_order_failed')],
                                'pending' => ['icon' => 'mdi-car-connected', 'label' => trans('lang.dashboard_order_pending')],
                            ];
                        @endphp

                        @foreach($statusCards as $key => $card)
                            <div class="col-sm-6 col-lg-3">
                                <div class="order-status">
                                    <div class="data">
                                        <i class="mdi {{ $card['icon'] }}"></i>
                                        <h6 class="status">{{ $card['label'] }}</h6>
                                    </div>
                                    <span class="count">{{ $statusCounts[$key] ?? 0 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ trans('lang.total_sales') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative">
                                <canvas id="sales-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fa fa-square" style="color:#2EC7D9"></i>
                                    {{ trans('lang.dashboard_this_year') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ trans('lang.service_overview') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="visitors" height="222"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header no-border">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">{{ trans('lang.sales_overview') }}</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="flex-row">
                                <canvas id="commissions" height="222"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row daes-sec-sec mb-3">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header no-border d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{ trans('lang.recent_orders') }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('orders') }}" class="btn btn-tool btn-sm">{{ trans('lang.view_all') }}</a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped table-valign-middle" id="orderTable">
                                <thead>
                                <tr>
                                    <th>{{ trans('lang.order_id') }}</th>
                                    <th>{{ trans('lang.order_user_id') }}</th>
                                    <th>{{ trans('lang.order_type') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th>{{ trans('lang.quantity') }}</th>
                                    <th>{{ trans('lang.order_date') }}</th>
                                    <th>{{ trans('lang.order_order_status_id') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="{{ $order['url'] }}">{{ $order['id'] }}</a></td>
                                        <td>{{ $order['customer'] }}</td>
                                        <td>{{ $order['type'] }}</td>
                                        <td>{{ $order['subtotal'] }}</td>
                                        <td><i class="fa fa-shopping-cart mr-1"></i>{{ $order['products'] }}</td>
                                        <td>{{ $order['date'] }}</td>
                                        <td class="{{ $order['status_class'] }}"><span>{{ $order['status'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            {{ trans('lang.no_record_found') }}
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/chart.js') }}"></script>
    <script>
        const salesLabels = @json($charts['sales']['labels']);
        const salesData = @json($charts['sales']['data']);
        const visitorsData = @json([$charts['visitors']['orders'], $charts['visitors']['products']]);
        const commissionsData = @json($charts['commission']['data']);
        const currencySymbol = @json($currencyMeta['symbol']);

        const ticksStyle = {
            fontColor: '#495057',
            fontStyle: 'bold'
        };

        const mode = 'index';
        const intersect = true;

        new Chart(document.getElementById('sales-chart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: salesLabels,
                datasets: [{
                    backgroundColor: '#2EC7D9',
                    borderColor: '#2EC7D9',
                    data: salesData
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: { mode, intersect },
                hover: { mode, intersect },
                legend: { display: false },
                scales: {
                    yAxes: [{
                        gridLines: {
                            display: true,
                            lineWidth: '4px',
                            color: 'rgba(0, 0, 0, .2)',
                            zeroLineColor: 'transparent'
                        },
                        ticks: Object.assign({
                            beginAtZero: true,
                            callback: (value) => currencySymbol + Number(value).toFixed(0)
                        }, ticksStyle)
                    }],
                    xAxes: [{
                        display: true,
                        gridLines: { display: false },
                        ticks: ticksStyle
                    }]
                }
            }
        });

        new Chart(document.getElementById('visitors'), {
            type: 'doughnut',
            data: {
                labels: [
                    "{{ trans('lang.dashboard_total_orders') }}",
                    "{{ trans('lang.dashboard_total_products') }}"
                ],
                datasets: [{
                    data: visitorsData,
                    backgroundColor: ['#B1DB6F', '#7360ed'],
                    hoverOffset: 4
                }]
            },
            options: { maintainAspectRatio: false }
        });

        new Chart(document.getElementById('commissions'), {
            type: 'doughnut',
            data: {
                labels: @json($charts['commission']['labels']),
                datasets: [{
                    data: commissionsData,
                    backgroundColor: ['#feb84d', '#9b77f8', '#fe95d3'],
                    hoverOffset: 4
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        label: (tooltipItems, data) => {
                            const dataset = data.datasets[tooltipItems.datasetIndex];
                            const value = dataset.data[tooltipItems.index] || 0;
                            return `${data.labels[tooltipItems.index]}: ${currencySymbol}${Number(value).toFixed(2)}`;
                        }
                    }
                }
            }
        });
    </script>
@endsection

