import XLSX from 'xlsx';
import fs from 'fs';

const filePath = './Gallo - Dados Atdr 2026.xlsx';
const fileBuffer = fs.readFileSync(filePath);
const workbook = XLSX.read(fileBuffer);

console.log('Abas encontradas:', workbook.SheetNames);

// Mostrar as últimas 3 abas para ver o conteúdo
const lastSheets = workbook.SheetNames.slice(-3);
lastSheets.forEach(name => {
    console.log(`\n--- Conteúdo da aba: ${name} ---`);
    const data = XLSX.utils.sheet_to_json(workbook.Sheets[name]);
    console.log(JSON.stringify(data.slice(0, 5), null, 2));
});
