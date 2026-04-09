# 📋 TABELA DE REFERÊNCIA RÁPIDA

Esta página contém **tabelas de referência rápida** para consultas imediatas.

---

## 📁 Arquivos De Documentação

| # | Arquivo | Tipo | Tema | Tempo | Para Quem |
|---|---------|------|------|-------|-----------|
| 1️⃣ | **README_DOCUMENTACAO.md** | 📖 Guia | Resumo executivo | 15 min | Todos |
| 2️⃣ | **FLUXO_SISTEMA.md** | 📚 Completo | Arquitetura + Fluxo | 60 min | Iniciantes/Intermediários |
| 3️⃣ | **COMENTARIOS_NUMERADOS.md** | 💻 Código | Código comentado | 90 min | Desenvolvedores |
| 4️⃣ | **ARQUITETURA_E_DADOS.md** | 🗄️ Dados | Banco + Full-Stack | 60 min | Arquitetos |
| 5️⃣ | **DIAGRAMAS_VISUAIS.md** | 📊 Visual | 10 diagramas ASCII | 20 min | Todos |
| 6️⃣ | **INDICE_DOCUMENTACAO.md** | 🗺️ Índice | Navegação + Índice | 15 min | Todos |
| 7️⃣ | **RESUMO_FINAL.md** | ✅ Resumo | Checklist + Próximos passos | 10 min | Todos |

---

## 🎯 Qual Arquivo Procurar?

| Sua Pergunta | Arquivo | Seção |
|--------------|---------|-------|
| "Como funciona o login?" | FLUXO_SISTEMA.md | ETAPA 1-5 |
| "Por que o atirador é forçado a fazer primeiro acesso?" | FLUXO_SISTEMA.md | ETAPA 6-8 |
| "Qual é o schema da tabela users?" | ARQUITETURA_E_DADOS.md | Tabela users |
| "Como funciona o hash de senha?" | ARQUITETURA_E_DADOS.md | Hash De Senha |
| "Onde verifico o código do AuthController?" | COMENTARIOS_NUMERADOS.md | AuthController.php |
| "Como funciona o middleware?" | FLUXO_SISTEMA.md | Estrutura De Middlewares |
| "Qual é o fluxo full-stack?" | ARQUITETURA_E_DADOS.md | Fluxo Full-Stack |
| "Preciso de um diagrama visual" | DIAGRAMAS_VISUAIS.md | Qualquer diagrama |
| "Como adiciono uma nova feature?" | INDICE_DOCUMENTACAO.md | Próximos Passos |
| "Qual documento ler primeiro?" | README_DOCUMENTACAO.md | COMECE AQUI |

---

## 🚀 Roteiros De Estudo

### Iniciante (1.5 horas)
```
1. README_DOCUMENTACAO.md              15 min
2. FLUXO_SISTEMA.md (Estrutura)        30 min
3. FLUXO_SISTEMA.md (Autenticação)     30 min
4. DIAGRAMAS_VISUAIS.md (Consulta)     15 min
```

### Desenvolvedor (3 horas)
```
1. README_DOCUMENTACAO.md              15 min
2. FLUXO_SISTEMA.md (Completo)         60 min
3. COMENTARIOS_NUMERADOS.md (Completo) 90 min
4. DIAGRAMAS_VISUAIS.md (Rápido)       20 min
5. Pausas | 15 min
```

### Arquiteto (3.5 horas)
```
1. README_DOCUMENTACAO.md              15 min
2. FLUXO_SISTEMA.md (Completo)         60 min
3. COMENTARIOS_NUMERADOS.md (Selectivo) 60 min
4. ARQUITETURA_E_DADOS.md (Completo)   60 min
5. INDICE_DOCUMENTACAO.md (Rápido)     15 min
6. Pausas | 20 min
```

---

## 🔐 Segurança Implementada

| Aspecto | Técnica | Detalhes |
|---------|---------|----------|
| **Autenticação** | RA/CPF + Senha | Busca por RA ou CPF, verifica hash |
| **Autorização** | Roles | atirador, instructor, master |
| **Senhas** | Bcrypt | Hash::make() + Hash::check() |
| **CSRF** | Token `@csrf` | Previne requisições falsificadas |
| **Sessão** | Regenerate | Nova sessão após login |
| **Cookies** | HttpOnly + SameSite | Proteção contra XSS |
| **Primeiro Acesso** | Forçado | Atirador novo é obrigado a configurar |

---

## 📊 Middleware Stack

| Ordem | Nome | Condição | Ação |
|-------|------|----------|------|
| 1️⃣ | `auth` | auth()->check() | Se falsa: redirect('login') |
| 2️⃣ | `first_access` | role='atirador' && email vazio | Se verdade: redirect('/primeiro-acesso') |
| 3️⃣ | `instructor` | role in ['master','instructor'] | Se falsa: redirect('dashboard') c/ erro |

---

## 👥 Roles E Permissões

| Papel | Acesso | Pode Fazer |
|-------|--------|-----------|
| **atirador** | Dashboard, Perfil, Avisos(R) | Ver avisos, Editar perfil |
| **instructor** | Tudo + Gestão | Gerenciar atiradores, CRUD avisos |
| **master** | TUDO | Acesso total ao sistema |

---

## 🗄️ Schema: Tabela `users`

| Campo | Tipo | Especial | Usar Para |
|-------|------|----------|-----------|
| `id` | INT | PK, AUTO_INCREMENT | Identificador único |
| `name` | VARCHAR(255) | - | Nome completo |
| `email` | VARCHAR(255) | UNIQUE, NULL | Email (NULL = novo atirador) |
| `password` | VARCHAR(255) | - | Senha (hasheada com Bcrypt) |
| `ra` | VARCHAR(255) | UNIQUE | Registro Acadêmico (login) |
| `cpf` | VARCHAR(255) | UNIQUE | CPF (login alternativo) |
| `role` | ENUM | atirador/instructor/master | Permissão |
| `nome_de_guerra` | VARCHAR(255) | UNIQUE | Codinome |
| `points` | INT | DEFAULT 0 | Pontos acumulados |
| `faults` | INT | DEFAULT 0 | Contagem de faltas |
| `photo` | VARCHAR(255) | NULL | Caminho da foto |
| `numero` | INT | - | Número do atirador |
| `turma` | VARCHAR(255) | - | Ano/Turma (ex: 2026) |
| `is_cfc` | BOOLEAN | DEFAULT FALSE | Tem CFC? |
| `telefone` | VARCHAR(255) | - | Contato |
| `created_at` | TIMESTAMP | AUTO | Data criação |
| `updated_at` | TIMESTAMP | AUTO | Data atualização |

---

## 🎯 Controllers Principais

| Controller | Método | Rota | O Que Faz |
|------------|--------|------|-----------|
| **AuthController** | showLogin() | GET / | Mostra formulário de login |
| | login() | POST /login | Autentica usuário |
| | logout() | POST /logout | Faz logout |
| **PrimeiroAcessoController** | index() | GET /primeiro-acesso | Mostra formulário |
| | store() | POST /primeiro-acesso | Salva email e senha |
| **ProfileController** | index() | GET /perfil | Mostra perfil |
| | update() | PUT /perfil | Atualiza perfil |
| **AtiradorController** | index() | GET /atiradores | Lista atiradores |
| | store() | POST /atiradores | Cria atirador |
| | update() | PUT /atiradores/{id} | Edita atirador |
| | toggleCfc() | PATCH /atiradores/{id}/toggle-cfc | Ativa/desativa CFC |
| | destroy() | DELETE /atiradores/{id} | Deleta atirador |
| **AnnouncementController** | index() | GET /avisos | Lista avisos |
| | show() | GET /avisos/{id} | Mostra aviso |
| | store() | POST /avisos | Cria aviso |
| | update() | PUT /avisos/{id} | Edita aviso |
| | destroy() | DELETE /avisos/{id} | Deleta aviso |

---

## 📍 Fluxo Em 10 Passos

| Etapa | Ação | Local | Resultado |
|-------|------|-------|-----------|
| 1️⃣ | Usuário acessa / | Browser | Vê página de login |
| 2️⃣ | Envia credenciais | login.blade.php | POST /login |
| 3️⃣ | Valida e autentica | AuthController | Hash::check() |
| 4️⃣ | Cria sessão | Session (servidor) | Usuário "logado" |
| 5️⃣ | Redireciona | Router | GET /dashboard |
| 6️⃣ | Middleware `auth` | CheckAuth | Passa ✅ |
| 7️⃣ | Middleware `first_access` | CheckFirstAccess | Email vazio? |
| 8️⃣ | Se SIM: primeiro acesso | primeiro_acesso.blade | Formulário |
| 9️⃣ | Se NÃO: dashboard | Dashboard.blade | Renderiza |
| 🔟 | Navegador renderiza | Browser | User vê página ✅ |

---

## 🔀 Decisões De Fluxo

| Verificação | Verdade | Falso |
|-------------|---------|-------|
| `auth()->check()` | Continua ✅ | Redireciona para login ❌ |
| `role === 'atirador'` | Pode ser atirador | Pode ser instrutor/master |
| `empty(email)` | Redireciona para /primeiro-acesso | Continua normalmente |
| `Hash::check()` | Login OK ✅ | Login Falha ❌ |
| `in_array(role, [...])` | Acima acesso ✅ | Bloqueia ❌ |

---

## 💾 Armazenamento De Dados

| Dados | Onde | Duração | Acessível |
|-------|------|---------|-----------|
| Sessão (logado) | storage/framework/sessions/ | 2h (config) | auth()->user() |
| Sessão (dados) | RAM server | requisição | session('key') |
| Cookies | Browser | Até expirar | $_COOKIE |
| Usuário (BD) | MySQL | Permanente | User::find() |
| Foto (arquivo) | storage/app/public/ | Até deletar | asset('storage/...') |

---

## 🔐 Password Workflow

| Etapa | Ação | Resultado |
|-------|------|-----------|
| 1️⃣ | User digita: "senha123" | String plain |
| 2️⃣ | Hash::make("senha123") | $2y$12$... (78 chars) |
| 3️⃣ | Salva no BD | UPDATE users SET password = '...' |
| 4️⃣ | Próximo login: User digita "senha123" | String plain |
| 5️⃣ | Hash::check("senha123", $hash) | Compara com BD |
| 6️⃣ | Se match | Login OK ✅ |
| 7️⃣ | Se não match | Login Falha ❌ |

---

## 📱 Tipos De Requisição

| Tipo | Para Quê | Exemplo Route | Controller |
|------|----------|---------------|-----------| 
| **GET** | Buscar dados | GET /perfil | ProfileController::index() |
| **POST** | Criar dados | POST /atiradores | AtiradorController::store() |
| **PUT** | Atualizar completo | PUT /perfil | ProfileController::update() |
| **PATCH** | Atualizar parcial | PATCH /atiradores/1/toggle-cfc | AtiradorController::toggleCfc() |
| **DELETE** | Deletar | DELETE /atiradores/1 | AtiradorController::destroy() |

---

## 🎓 Conceitos Unificados

| Conceito | Significa | Exemplo |
|----------|-----------|---------|
| **Route** | Mapeamento de URL | GET /dashboard → DashboardCtrl |
| **Middleware** | Filtro de requisição | auth → verifica autenticação |
| **Controller** | Processador de requisição | AuthController::login() |
| **Model** | Representação de tabela | User (SQL: users) |
| **View** | Template HTML | dashboard.blade.php |
| **Request** | Dados da requisição | $request->validate() |
| **Response** | Resposta do servidor | return view(...) |
| **Session** | Dados persistidos | session('key') |

---

## ✅ Checklist: Você Aprendeu Se Consegue...

- [ ] Explicar o ciclo completo de um login
- [ ] Desenhar um diagrama de fluxo
- [ ] Explicar o papel de cada middleware
- [ ] Entender por que senhas são hasheadas
- [ ] Diferenciar entre roles
- [ ] Localizar um controller pelo nome
- [ ] Adicionar um novo campo ao User
- [ ] Criar uma nova rota protegida
- [ ] Debugar um problema seguindo o fluxo
- [ ] Ensinar o sistema a alguém

---

## 🌐 MVC Simplificado

```
REQUEST
   ↓
ROUTER (routes/web.php)
   ↓
MIDDLEWARE (auth, first_access, etc)
   ↓
CONTROLLER (AuthController, etc)
   ↓
MODEL (User, Announcement, etc)
   ↓
DATABASE (MySQL)
   ↓
MODEL (volta com dados)
   ↓
VIEW (dashboard.blade.php)
   ↓
HTML RENDERIZADO
   ↓
BROWSER (usuário vê página)
```

---

## 🚀 Próximas Ações

| Objetivo | Arquivo | Primeiro Passo |
|----------|---------|---|
| Adicionar feature | COMENTARIOS_NUMERADOS.md | Encontre um controller similar |
| Debugar bug | DIAGRAMAS_VISUAIS.md | Siga o fluxo até encontrar |
| Ensinar alguém | FLUXO_SISTEMA.md | Comece pelos conceitos |
| Modificar banco | ARQUITETURA_E_DADOS.md | Veja o schema |
| Entender rápido | README_DOCUMENTACAO.md | Leia em 15 minutos |

---

## 📞 Referência Rápida Por Tópico

### Autenticação
- **Arquivo:** FLUXO_SISTEMA.md
- **Seção:** "Fluxo De Autenticação E Primeiro Acesso"
- **Código:** COMENTARIOS_NUMERADOS.md → AuthController.php

### Primeiro Acesso
- **Arquivo:** FLUXO_SISTEMA.md
- **Seção:** "ETAPA 6️⃣ a ETAPA 8️⃣"
- **Código:** COMENTARIOS_NUMERADOS.md → PrimeiroAcessoController.php

### Middlewares
- **Arquivo:** FLUXO_SISTEMA.md
- **Seção:** "Estrutura De Middlewares"
- **Código:** COMENTARIOS_NUMERADOS.md → Ambos

### Roles
- **Arquivo:** FLUXO_SISTEMA.md
- **Seção:** "Sistema De Roles E Permissões"
- **Código:** COMENTARIOS_NUMERADOS.md → CheckInstructorRole.php

### Banco De Dados
- **Arquivo:** ARQUITETURA_E_DADOS.md
- **Seção:** "Tabela De Banco De Dados"
- **Schema:** Completo com tipos

### Fluxo Visual
- **Arquivo:** DIAGRAMAS_VISUAIS.md
- **Diagrama 1:** Login completo
- **Diagrama 7:** MVC

---

**Última atualização:** 9 de Abril de 2026
**Status:** ✅ Completo e Atualizado

