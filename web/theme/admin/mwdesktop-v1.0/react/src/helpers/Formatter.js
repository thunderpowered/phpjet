export class Formatter {
    formatRelativeURL(urlString) {
        while (urlString.indexOf('/') === 0) {
            urlString = urlString.substring(1);
        }
        return '/' + urlString;
    }
}