import {getCookie} from "./tools/cookie";
import {IS_BROWSER} from "./constants/Misc";
import i18next from "i18next";
import common_en from "./translations/en/common.json";
import i18n from "i18next";
import {initReactI18next} from "react-i18next";

export const LANGUAGES = ['en']; // add more if needed

export const getSavedLanguage = () => {
    return getCookie('language');
};

export const getClientLanguage = (checkIfSupported = true) => {
    if (IS_BROWSER) {
        let lang = navigator.language || document.getElementsByTagName('html')[0].lang
        if (lang) {
            lang = lang.match(/[a-z]{2,3}/g) || []
            lang = lang[0];
        }

        if (checkIfSupported) {
            return LANGUAGES.includes(lang) ? lang : null;
        } else {
            return lang;
        }
    }
};

export const DEFAULT_LANGUAGE = LANGUAGES[0];
export const LANGUAGE = getSavedLanguage() ?? getClientLanguage() ?? DEFAULT_LANGUAGE;

i18n
    .use(initReactI18next)
    .init({
        interpolation: {escapeValue: false},
        lng: LANGUAGE,
        fallbackLng: DEFAULT_LANGUAGE,
        resources: {
            en: {
                common: common_en // todo add other languages
            }
        }
    });

export default i18n;