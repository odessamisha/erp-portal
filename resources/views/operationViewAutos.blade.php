
<div>
    <ul>
        @foreach($record->operation_autos as $autos)
            <li>
                {{ $autos->auto_name }} --- {{ $autos->price }} грн.
            </li>
        @endforeach
    </ul>
</div>