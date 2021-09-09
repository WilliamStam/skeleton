export const replaceObjectValues = (obj1: {[key: string]:unknown} = {},obj2 : {[key: string]:unknown} = {}) : Record<string, unknown> => {

    Object.keys(obj1).forEach(k => {
        if (k in obj2){
            obj1[k] = obj2[k]
        }
    })

    return obj1;
}