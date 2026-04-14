function aggregateFlexible(
    data,
    fieldTgl,
    config = [],
    periodeFormat = "MM",
    limit = 30
) {

    // =========================
    // PARSE DATE
    // =========================
    function parseTanggal(str) {
        if (!str) return null;
        const parts = String(str).split(".");
        if (parts.length !== 3) return null;
        return new Date(`${parts[2]}-${parts[1]}-${parts[0]}`);
    }

    // =========================
    // INIT MAP
    // =========================
    let map = {};

    // =========================
    // BULAN FIX (JAN-DEC)
    // =========================
    const bulanLengkap = ["01","02","03","04","05","06","07","08","09","10","11","12"];
    const namaBulan    = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];

    if (periodeFormat === "MM") {
        bulanLengkap.forEach(b => {
            map[b] = { _date: null };
            config.forEach(c => {
                map[b][c.key] = 0;
            });
        });
    }

    // =========================
    // LOOP DATA
    // =========================
    data.forEach(item => {

        const tglRaw = item[fieldTgl];
        if (!tglRaw) return;

        const parts = String(tglRaw).split(".");
        if (parts.length !== 3) return;

        let key;

        switch (periodeFormat) {

            case "MM":
                key = parts[1]; // bulan
                break;

            case "YYYY":
                key = parts[2];
                break;

            case "YYYY-MM":
                key = `${parts[2]}-${parts[1]}`;
                break;

            case "DD.MM.YYYY":
                key = `${parts[0]}.${parts[1]}.${parts[2]}`;
                break;

            default:
                key = parts[1];
        }

        if (!map[key]) {
            map[key] = { _date: parseTanggal(tglRaw) };
            config.forEach(c => {
                map[key][c.key] = 0;
            });
        }

        config.forEach(c => {

            let value = 0;

            if (c.type === "count") {
                value = 1;
            }

            if (c.type === "sum") {
                if (Array.isArray(c.field)) {
                    c.field.forEach(f => {
                        value += parseFloat(item[f]) || 0;
                    });
                } else {
                    value += parseFloat(item[c.field]) || 0;
                }
            }

            map[key][c.key] += value;
        });
    });

    // =========================
    // RESULT ARRAY
    // =========================
    let result = Object.keys(map).map(k => ({
        periode: k,
        _date: map[k]._date,
        ...map[k]
    }));

    // =========================
    // MM MODE → FORCE JAN-DEC ORDER
    // =========================
    if (periodeFormat === "MM") {

        return bulanLengkap.map((b, i) => {

            let obj = {
                periode: namaBulan[i]
            };

            config.forEach(c => {
                obj[c.key] = map[b]?.[c.key] || 0;
            });

            return obj;
        });
    }

    // =========================
    // SORT DATE
    // =========================
    result.sort((a, b) => (a._date || 0) - (b._date || 0));

    // =========================
    // LIMIT (NON-MM ONLY)
    // =========================
    if (limit && periodeFormat !== "MM") {
        result = result.slice(-limit);
    }

    // =========================
    // CLEAN OUTPUT
    // =========================
    return result.map(r => {
        const obj = { periode: r.periode };

        config.forEach(c => {
            obj[c.key] = r[c.key] || 0;
        });

        return obj;
    });
}