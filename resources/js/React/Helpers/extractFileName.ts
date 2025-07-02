const extractFileName = ((url: string, debug=false): string => {
    console.log("ExtractFileName Input: ", url);

    // if url is not a string, return an empty string
    if (typeof url !== "string") return "";
   
    try {
        const cache = extractFileName.cache || (extractFileName.cache = {});
        if (cache[url]) {
            debug && console.log("Cache Hit for URL: ", url);
            return cache[url];
        }

       
        const fileName = new URL(url).pathname.split("/").pop();
        cache[url] = fileName as string; // Add type assertion
        debug && console.log("ExtractFileName Output: ", fileName);
        return fileName || ""; // Handle undefined case
    } catch (error) {
        console.error("Error extracting file name from URL:", error);
        return "";
    }
}) as ((url: string) => string) & { cache?: Record<string, string> };

export default extractFileName;