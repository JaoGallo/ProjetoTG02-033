import XLSX from 'xlsx';
import fs from 'fs';

const filePath = './Gallo - Dados Atdr 2026.xlsx';
try {
    const fileBuffer = fs.readFileSync(filePath);
    const workbook = XLSX.read(fileBuffer);
    const sheetName = workbook.SheetNames[0];
    const sheet = workbook.Sheets[sheetName];
    const data = XLSX.utils.sheet_to_json(sheet, { header: 1 });
    
    if (data.length > 0) {
        console.log('Headers found:', JSON.stringify(data[0]));
        console.log('First row sample:', JSON.stringify(data[1]));
    } else {
        console.log('No data found in Excel.');
    }
} catch (err) {
    console.error('Error reading file:', err.message);
}
