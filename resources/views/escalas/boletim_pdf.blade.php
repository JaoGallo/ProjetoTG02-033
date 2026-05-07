<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $config->nome }}</title>
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
            text-transform: uppercase;
        }
        .intro-text {
            text-align: justify;
            text-indent: 2cm;
            margin-bottom: 20px;
        }
        .parte-title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0 10px 0;
            text-transform: uppercase;
        }
        .escala-date {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10pt;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 4px 8px;
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
            page-break-inside: avoid;
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
        .part-content {
            margin-bottom: 20px;
            text-align: justify;
            white-space: pre-wrap;
        }
        .part-content-empty {
            font-style: italic;
            color: #555;
            text-align: center;
            margin-bottom: 20px;
        }
        .page-break {
            page-break-before: always;
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
        {{ mb_strtoupper($config->nome, 'UTF-8') }}
    </div>

    <div class="intro-text">
        Para conhecimento deste Tiro-de-Guerra e devida execução, publico o seguinte:
    </div>

    <div class="parte-title">
        1ª PARTE - SERVIÇOS DIÁRIOS
    </div>

    @foreach($dias as $dia)
    <div class="escala-date" style="page-break-inside: avoid;">
        ESCALA DE SERVIÇO PARA O DIA {{ $dia['data']->format('d') }} de {{ strtoupper($dia['data']->translatedFormat('F')) }} DE {{ $dia['data']->format('Y') }} ({{ strtoupper($dia['data']->translatedFormat('l')) }})
    </div>

    <table style="page-break-inside: avoid;">
        <thead>
            <tr>
                <th style="width: 30%;">FUNÇÃO</th>
                <th style="width: 25%;">Grad - Nr</th>
                <th style="width: 45%;">NOME</th>
            </tr>
        </thead>
        <tbody>
            <!-- Comandantes -->
            @foreach($dia['mon'] as $idx => $reg)
            <tr>
                @if($idx === 0)
                    <td rowspan="{{ count($dia['mon']) }}" style="font-weight: bold;">Cmt Gd</td>
                @endif
                <td>Mon - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ mb_strtoupper($reg->user->nome_de_guerra, 'UTF-8') }}</td>
            </tr>
            @endforeach

            <!-- Sentinelas -->
            @foreach($dia['atdr'] as $idx => $reg)
            <tr>
                @if($idx === 0)
                    <td rowspan="{{ count($dia['atdr']) }}" style="font-weight: bold;">Sentinelas</td>
                @endif
                <td>Atdr - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                <td style="font-weight: bold;">{{ mb_strtoupper($reg->user->nome_de_guerra, 'UTF-8') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endforeach

    <div class="parte-title">
        2ª PARTE - INSTRUÇÃO
    </div>
    @if(!empty($config->part2_instrucao))
        <div class="part-content">{!! nl2br(e($config->part2_instrucao)) !!}</div>
    @else
        <div class="part-content-empty">(Sem Alteração)</div>
    @endif

    <div class="parte-title">
        3ª PARTE - ASSUNTOS GERAIS
    </div>
    @if(!empty($config->part3_assuntos_gerais))
        <div class="part-content">{!! nl2br(e($config->part3_assuntos_gerais)) !!}</div>
    @else
        <div class="part-content-empty">(Sem Alteração)</div>
    @endif

    <div class="parte-title">
        4ª PARTE - JUSTIÇA E DISCIPLINA
    </div>
    @if(!empty($config->part4_justica_disciplina))
        <div class="part-content">{!! nl2br(e($config->part4_justica_disciplina)) !!}</div>
    @else
        <div class="part-content-empty">(Sem Alteração)</div>
    @endif

    <div class="footer">
        <div class="signature-line"></div>
        <div style="font-weight: bold;">Chefe da Instrução do TG 02-033</div>
        <div style="font-size: 10pt; margin-top: 5px;">S. J. do Rio Preto - SP, {{ $config->data_fim->format('d') }} de {{ \Carbon\Carbon::parse($config->data_fim)->translatedFormat('F') }} de {{ $config->data_fim->format('Y') }}.</div>
    </div>
</body>
</html>
