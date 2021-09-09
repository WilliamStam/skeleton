export const formatNumber = (value: number | string, decimals = 2): string => {
    if (typeof value == "string") {
        value = parseFloat(value);
    }

    if (decimals) {
        value = value.toFixed(decimals);
    }
    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}