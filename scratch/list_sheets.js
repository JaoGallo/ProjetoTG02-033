import XLSX from 'xlsx';
import fs from 'fs';

const filePath = './Gallo - Dados Atdr 2026.xlsx';
const fileBuffer = fs.readFileSync(filePath);
const workbook = XLSX.read(fileBuffer);

console.log('Todas as abas:', workbook.SheetNames);
