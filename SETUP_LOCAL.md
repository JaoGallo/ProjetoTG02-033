# 🚀 Guia Completo: Rodar o Projeto Localmente

## ⚠️ Status Atual
- ❌ PHP não está instalado ou não está no PATH
- ✅ Projeto Laravel existe em: `C:\Users\Public\Documents\ProjetoTG02-033`
- ✅ Arquivo `.env` foi corrigido com `APP_URL=http://localhost:8000`

---

## 📋 Opção 1: Usar Laravel Herd (RECOMENDADO - Mais Fácil)

### Passo 1: Instale Laravel Herd
1. Acesse: https://herd.laravel.com/
2. Baixe a versão para Windows
3. Execute o instalador
4. Siga as instruções (ele instala PHP, Node.js, tudo automaticamente)

### Passo 2: Configure o Projeto
```powershell
# Abra PowerShell na pasta do projeto
cd 'C:\Users\Public\Documents\ProjetoTG02-033'

# Instale dependências PHP
composer install

# Gere a chave de aplicação
php artisan key:generate

# Migre banco de dados
php artisan migrate:fresh --seed

# Inicie o servidor (rodará em http://localhost)
php artisan serve
```

---

## 📋 Opção 2: Instalar PHP Manualmente

### Passo 1: Baixe PHP
1. Acesse: https://windows.php.net/download/
2. Baixe a versão VC17 x64 Non Thread Safe (ou Latest)
3. Descompacte em: `C:\php` (ou outro local)

### Passo 2: Adicione PHP ao PATH do Windows
1. **Windows + X** → Selecione "Sistema"
2. Clique em **"Configurações avançadas do sistema"**
3. Clique em **"Variáveis de Ambiente"**
4. Em "Variáveis do sistema", procure por **PATH**
5. Clique **Editar**
6. Clique **Novo** e adicione: `C:\php`
7. Clique **OK** → **OK** → **OK**
8. **Reinicie o PowerShell**

### Passo 3: Instale Composer
1. Acesse: https://getcomposer.org/download/
2. Execute o instalador (escolha seu PHP em `C:\php`)
3. Deixe tudo como padrão

### Passo 4: Configure o Projeto
```powershell
cd 'C:\Users\Public\Documents\ProjetoTG02-033'
composer install
php artisan migrate:fresh --seed
php artisan serve
```

---

## 📋 Opção 3: Usar Docker (Para Quem Quer Ambiente Isolado)

### Passo 1: Instale Docker Desktop
1. Acesse: https://www.docker.com/products/docker-desktop
2. Baixe e instale
3. Reinicie o computador

### Passo 2: Use Sail (Docker do Laravel)
```powershell
cd 'C:\Users\Public\Documents\ProjetoTG02-033'

# Instale Sail
composer require laravel/sail --dev

# Inicie com Docker
./vendor/bin/sail up
```

O projeto rodará em: `http://localhost`

---

## ✅ Verificar Instalação

```powershell
# Testar PHP
php --version

# Testar Composer
composer --version
```

Ambos devem retornar versões (não "comando não encontrado").

---

## 🎯 Rodar o Servidor

Após instalar, em uma **nova janela PowerShell**:

```powershell
cd 'C:\Users\Public\Documents\ProjetoTG02-033'
php artisan serve
```

**Você verá:**
```
   INFO  Server running on [http://127.0.0.1:8000]
```

---

## 🌐 Acessar a Aplicação

Abra no navegador: **http://localhost:8000**

Se precisar de uma porta diferente:
```powershell
php artisan serve --port=3000
# Depois acesse: http://localhost:3000
```

---

## 🔑 Dados de Teste (Após Migrate --seed)

**Login padrão:**
- Email: `admin@test.com`
- Senha: `password`

---

## ❌ Problemas Comuns

### "composer: The term 'composer' is not recognized"
→ Reinstale Composer após adicionar PHP ao PATH

### "migration failed: unable to open database file"
→ Execute: `php artisan migrate:fresh --seed`

### "Port 8000 already in use"
→ Use outra porta: `php artisan serve --port=3000`

### "npm not found" (se quiser compilar assets)
→ Baixe Node.js: https://nodejs.org/ (LTS)
→ Então: `npm install && npm run build`

---

## 📞 Precisa de Ajuda?

Após tentar uma das opções, execute este comando e me mostre a saída:

```powershell
php --version
composer --version
cd 'C:\Users\Public\Documents\ProjetoTG02-033' && php artisan --version
```

Assim saberei exatamente qual é o estado!
