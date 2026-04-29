<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aditamento - {{ $carbon->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            margin: 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .header p {
            margin: 0;
            padding: 0;
        }
        .aditamento-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        .intro-text {
            text-align: justify;
            text-indent: 2cm;
            margin-bottom: 20px;
        }
        .parte-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }
        .escala-date {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 5px 10px;
            text-align: center;
        }
        th {
            font-weight: bold;
            background-color: #f2f2f2;
            text-transform: uppercase;
        }
        td.align-left {
            text-align: left;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
        }
        .signature-line {
            width: 300px;
            border-top: 1px solid #000;
            margin: 0 auto 5px auto;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.04);
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="watermark">TG 02-033</div>

    <div class="header">
        <p>MINISTÉRIO DA DEFESA</p>
        <p>EXÉRCITO BRASILEIRO</p>
        <p>CMSE - CMDO 2ª RM</p>
        <p>SEÇÃO DE TIRO DE GUERRA E ESCOLA DE INSTRUÇÃO MILITAR</p>
        <p>TIRO DE GUERRA 02-033 (SÃO JOSÉ DO RIO PRETO - SP)</p>
    </div>

    <div class="aditamento-title">
        ADITAMENTO/TG NR {{ \Carbon\Carbon::now()->weekOfYear }}/{{ date('Y') }} – TG 02-033
    </div>

    <div class="intro-text">
        Para conhecimento deste Tiro-de-Guerra e devida execução, publico o seguinte:
    </div>

    <div class="parte-title">
        1ª PARTE - SERVIÇOS DIÁRIOS
    </div>

    <div class="escala-date">
        ESCALA DE SERVIÇO PARA O DIA {{ $carbon->format('d') }} de {{ strtoupper($carbon->translatedFormat('F')) }} ({{ strtoupper($carbon->translatedFormat('l')) }})
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">FUNÇÃO</th>
                <th style="width: 25%;">Grad - Nr</th>
                <th style="width: 45%;">NOME</th>
            </tr>
        </thead>
        <tbody>
            <!-- Comandantes -->
            @foreach($dados['mon'] as $idx => $reg)
            <tr>
                @if($idx === 0)
                    <td rowspan="{{ count($dados['mon']) }}" style="font-weight: bold;">Cmt Gd</td>
                @endif
                <td>Mon - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ $reg->user->nome_de_guerra }}</td>
            </tr>
            @endforeach

            <!-- Sentinelas -->
            @foreach($dados['atdr'] as $idx => $reg)
            <tr>
                @if($idx === 0)
                    <td rowspan="{{ count($dados['atdr']) }}" style="font-weight: bold;">Sentinelas</td>
                @endif
                <td>Atdr - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ $reg->user->nome_de_guerra }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-line"></div>
        <div style="font-weight: bold;">Chefe da Instrução do TG 02-033</div>
        <div style="font-size: 10pt; margin-top: 5px;">S. J. do Rio Preto - SP, {{ date('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de {{ date('Y') }}.</div>
    </div>
</body>
</html>
