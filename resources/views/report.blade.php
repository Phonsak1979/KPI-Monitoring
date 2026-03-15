<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard ตัวชี้วัด ANC') }}
            </h2>
            <form action="{{ route('sync.api') }}" method="POST">
                @csrf
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    ดึงข้อมูลล่าสุด (Sync API)
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-4 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <canvas id="ancChart" height="100"></canvas>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-3 border">รหัสพื้นที่ (อำเภอ)</th>
                            <th class="p-3 border">เป้าหมาย (Target)</th>
                            <th class="p-3 border">ผลงาน (Result)</th>
                            <th class="p-3 border">ร้อยละ (%)</th>
                            <th class="p-3 border">ประเมินผล</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summaryData as $row)
                        <tr>
                            <td class="p-3 border">{{ $row->areacode }}</td>
                            <td class="p-3 border">{{ number_format($row->total_target) }}</td>
                            <td class="p-3 border">{{ number_format($row->total_result) }}</td>
                            <td class="p-3 border">{{ $row->percent }} %</td>
                            <td class="p-3 border font-bold {{ $row->status == 'ผ่านเกณฑ์' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $row->status }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('ancChart').getContext('2d');
        const ancChart = new Chart(ctx, {
            type: 'bar', // เปลี่ยนเป็น 'line', 'pie' ได้
            data: {
                labels: {!! json_encode($labels) !!}, // ดึงข้อมูล labels จาก controller
                datasets: [{
                    label: 'ร้อยละผลงานแยกตามอำเภอ',
                    data: {!! json_encode($data) !!}, // ดึงข้อมูล % จาก controller
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, max: 100 }
                }
            }
        });
    </script>
</x-app-layout>