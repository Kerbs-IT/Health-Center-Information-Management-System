@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @php
            $logoPath = public_path('images/hugo_perez_logo.png');
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc = 'data:image/png;base64,' . $logoData;
            @endphp
            <img src="{{ $logoSrc }}"
                alt="Health Center IMS"
                style="width: 60px; height: 60px; border-radius: 50%;">
        </a>
    </td>
</tr>