<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $config->nome }}</title>
    <style>
        @page {
            margin: 1.0cm 2.5cm 1.0cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 8pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            text-transform: uppercase;
        }
        .header .logo {
            width: 55px;
            height: auto;
            margin-bottom: 5px;
        }
        .header p {
            margin: 0;
            padding: 0;
            font-weight: bold;
            font-size: 9pt;
        }
        .aditamento-title {
            text-align: center;
            font-weight: bold;
            margin: 12px 0;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 10pt;
        }
        .intro-text {
            text-align: center;
            margin-bottom: 12px;
        }
        .parte-title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0 8px 0;
            text-transform: uppercase;
            font-size: 9pt;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        .escala-container {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .escala-date {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-size: 8pt;
            display: block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 3px 6px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            font-weight: bold;
            background-color: #f2f2f2;
            text-transform: uppercase;
        }
        td.funcao-col {
            font-weight: bold;
            width: 25%;
        }
        td.nr-col {
            width: 25%;
        }
        td.nome-col {
            text-align: center;
            font-weight: bold;
            width: 50%;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            page-break-inside: avoid;
        }
        .signature-line {
            width: 250px;
            border-top: 1.5px solid #000;
            margin: 0 auto 5px auto;
        }
        .signature-name {
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .signature-rank {
            font-size: 8pt;
        }
        .signature-date {
            margin-top: 8px;
            font-size: 8pt;
            font-style: italic;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 70pt;
            color: rgba(0, 0, 0, 0.02);
            z-index: -1;
            white-space: nowrap;
        }
        .part-content {
            margin-bottom: 15px;
            text-align: justify;
            text-indent: 1.5cm;
            white-space: pre-wrap;
        }
        .part-content-empty {
            font-style: italic;
            color: #444;
            text-align: center;
            margin: 8px 0;
        }
        .empty-table-msg {
            text-align: center;
            font-style: italic;
            padding: 6px;
        }
    </style>
</head>
<body>
    <div class="watermark">TG 02-033</div>

    <div class="header">
        <img src="{{ public_path('AdtLogo.png') }}" class="logo">
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
    <div class="escala-container">
        <span class="escala-date">
            ESCALA DE SERVIÇO PARA O DIA {{ $dia['data']->format('d') }} de {{ strtoupper($dia['data']->translatedFormat('F')) }} DE {{ $dia['data']->format('Y') }} ({{ strtoupper($dia['data']->translatedFormat('l')) }})
        </span>

        @if($dia['mon']->isEmpty() && $dia['atdr']->isEmpty())
            <div class="empty-table-msg">Sem serviço escalado para esta data.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>FUNÇÃO</th>
                        <th>GRAD - NR</th>
                        <th>NOME DE GUERRA</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Comandantes -->
                    @if($dia['mon']->isNotEmpty())
                        @foreach($dia['mon'] as $idx => $reg)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ count($dia['mon']) }}" class="funcao-col">Cmt Gd</td>
                            @endif
                            <td class="nr-col">MON - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="nome-col">{{ mb_strtoupper($reg->user->nome_de_guerra ?? $reg->user->name, 'UTF-8') }}</td>
                        </tr>
                        @endforeach
                    @endif

                    <!-- Sentinelas -->
                    @if($dia['atdr']->isNotEmpty())
                        @foreach($dia['atdr'] as $idx => $reg)
                        <tr>
                            @if($idx === 0)
                                <td rowspan="{{ count($dia['atdr']) }}" class="funcao-col">SENTINELAS</td>
                            @endif
                            <td class="nr-col">ATDR - {{ str_pad($reg->user->numero, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="nome-col">{{ mb_strtoupper($reg->user->nome_de_guerra ?? $reg->user->name, 'UTF-8') }}</td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        @endif
    </div>
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
    @if(!empty(trim($config->part4_justica_disciplina ?? '')))
        <div class="part-content">{!! nl2br(e($config->part4_justica_disciplina)) !!}</div>
    @else
        <div class="part-content-empty">(Sem Alteração)</div>
    @endif

    <div class="footer">
        <div class="signature-line"></div>
        <div class="signature-name">{{ $config->creator->name ?? 'Chefe da Instrução' }}</div>
        <div class="signature-rank">Tiro de Guerra 02-033</div>
        <div class="signature-date">S. J. do Rio Preto - SP, {{ date('d') }} de {{ \Carbon\Carbon::now()->translatedFormat('F') }} de {{ date('Y') }}.</div>
    </div>
</body>
</html>
