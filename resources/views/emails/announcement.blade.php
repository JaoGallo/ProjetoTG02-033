<x-mail::message>
# Novo Aviso Oficial: {{ $announcement->title }}

Olá, **{{ $user->name }}**.

O Quartel-General publicou um novo comunicado para a sua turma que requer a sua atenção imediata.

<x-mail::panel>
**Categoria:** {{ strtoupper($announcement->category) }}  
**Resumo:** {{ Str::limit($announcement->content, 150) }}
</x-mail::panel>

@if($announcement->priority)
> [!IMPORTANT]
> Este aviso foi marcado como **URGENTE**. Por favor, leia os detalhes completos no sistema.
@endif

Para visualizar o conteúdo completo, anexos e diretrizes, clique no botão abaixo:

<x-mail::button :url="$url" color="success">
Acessar Aviso no Sistema
</x-mail::button>

Atenciosamente,  
**Comando do Tiro de Guerra 02-033**

---
Se você não conseguir clicar no botão, copie e cole o link abaixo no seu navegador:  
[{{ $url }}]({{ $url }})
</x-mail::message>
