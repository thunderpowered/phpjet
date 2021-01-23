export function isBrowser() {
    //https://stackoverflow.com/questions/9847580/how-to-detect-safari-chrome-ie-firefox-and-opera-browser
    return !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);
}