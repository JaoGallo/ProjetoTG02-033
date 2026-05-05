import XLSX from 'xlsx';
import fs from 'fs';

const filePath = './temp_gallo.xlsx';
const fileBuffer = fs.readFileSync(filePath);
const workbook = XLSX.read(fileBuffer);
const data = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);

if (data.length > 0) {
    console.log('Colunas encontradas:', Object.keys(data[0]));
} else {
    console.log('Nenhum dado encontrado no Excel.');
}
