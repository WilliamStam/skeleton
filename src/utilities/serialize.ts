export const objectToQueryString = ( obj: {
   [key: string]: number | string | boolean,
  } ): string => {
    const str = Object.keys(obj).reduce((a:string[] = [], k:string) => {
        a.push(k + '=' + encodeURIComponent( obj[k] ));
        return a;
    }, []).join('&');
    return str;
}