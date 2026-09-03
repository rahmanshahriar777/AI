<table class="table table-bordered">
    <thead>
        <tr>
            <th></th>
            <th></th>
            <th>Checklist</th>
            <th>Attributes</th>
            @foreach ($vehicleoverviews as $overview)
                <th>
                    {{ \Carbon\Carbon::parse($overview->overview_date)->format('M-y') }}
                    <br />
                    Mileage: {{ $overview->mileage }} km
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($vehiclechecklists as $category)
            @php
                $categoryRowspan =
                    $category->child->sum(
                        fn($child) => $child->checklists->sum(
                            fn($checklist) => max($checklist->attributes->count(), 1),
                        ),
                    ) ?:
                    1;
            @endphp

            @if ($category->child->count())
                @php $firstCategoryRow = true; @endphp
                @foreach ($category->child as $child)
                    @php
                        $childRowspan =
                            $child->checklists->sum(fn($checklist) => max($checklist->attributes->count(), 1)) ?: 1;
                    @endphp
                    @php $firstChildRow = true; @endphp

                    @foreach ($child->checklists as $checklist)
                        @php $checklistRowspan = max($checklist->attributes->count(), 1); @endphp
                        @php $firstChecklistRow = true; @endphp

                        @foreach ($checklist->attributes->count() ? $checklist->attributes : [null] as $attribute)
                            @php
                                $key =
                                    $category->id .
                                    '-' .
                                    $child->id .
                                    '-' .
                                    $checklist->id .
                                    '-' .
                                    ($attribute->id ?? 0);
                            @endphp
                            <tr>
                                {{-- Category --}}
                                @if ($firstCategoryRow)
                                    <td rowspan="{{ $categoryRowspan }}">{{ $category->name }}</td>
                                    @php $firstCategoryRow = false; @endphp
                                @endif

                                {{-- Child --}}
                                @if ($firstChildRow)
                                    <td rowspan="{{ $childRowspan }}">{{ $child->name }}</td>
                                    @php $firstChildRow = false; @endphp
                                @endif

                                {{-- Checklist --}}
                                @if ($firstChecklistRow)
                                    <td rowspan="{{ $checklistRowspan }}">{{ $checklist->title }}
                                    </td>
                                    @php $firstChecklistRow = false; @endphp
                                @endif

                                {{-- Attribute --}}
                                <td>{{ $attribute?->attribute_name ?? '' }}</td>

                                {{-- Overview values --}}
                                @foreach ($overviewDates as $date)
                                    @php
                                        $detail = $overviewMap[$key][$date] ?? null;
                                        $display = '-';

                                        if ($detail?->overview_value == 1) {
                                            $display = 'OK ' . (!empty($detail?->notes) ? e($detail->notes) : '');
                                        } elseif ($detail?->overview_value == 2) {
                                            $display = '' . (!empty($detail?->notes) ? e($detail->notes) : '');
                                        } elseif ($detail?->overview_value == 0) {
                                            $display = 'N/A' . (!empty($detail?->notes) ? e($detail->notes) : '');
                                        }
                                    @endphp
                                    <td>{{ $display }}</td>
                                @endforeach

                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            @else
                {{-- Category with no children --}}
                <tr>
                    <td>{{ $category->name }}</td>
                    <td colspan="2">-</td>
                    <td>-</td>
                    @foreach ($overviewDates as $date)
                        <td>-</td>
                    @endforeach
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
