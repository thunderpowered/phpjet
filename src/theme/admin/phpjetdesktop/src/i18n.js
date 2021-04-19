import {getCookie, setCookie} from "./tools/cookie";
import {IS_BROWSER} from "./constants/Misc";
import common_en from "./translations/en/common.json";
import common_ru from "./translations/ru/common.json";
import i18n from "i18next";
import {initReactI18next} from "react-i18next";

const cookieName = 'language';

export const LANGUAGES = ['en', 'ru']; // add more if needed
export const LANGUAGE_LABELS = {
    'en' : {
        'label' : 'ENG',
        'icon' : '/common/ff-us.svg'
    },
    'ru' : {
        'label' : 'РУС',
        'icon' : '/common/ff-ru.svg'
    }
};

export const getSavedLanguage = () => {
    return getCookie(cookieName);
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

export const changeLanguage = (lang, saveLang = true) => {
    console.log(lang, `save: ${saveLang}`);
    if (LANGUAGES.includes(lang)) {
        i18n.changeLanguage(lang);
        if (saveLang) {
            setCookie(cookieName, lang, 365);
        }
    } else {
        console.error(`${lang} is not a supported language`);
    }
}

export const DEFAULT_LANGUAGE = LANGUAGES[0];
export const getLanguage = () => getSavedLanguage() ?? getClientLanguage() ?? DEFAULT_LANGUAGE;


i18n
    .use(initReactI18next)
    .init({
        interpolation: {escapeValue: false},
        lng: getLanguage(),
        fallbackLng: DEFAULT_LANGUAGE,
        resources: {
            en: {
                common: common_en
            },
            ru: {
                common: common_ru
            }
        },

        ns: ["common"],
        defaultNS: "common"
    });

export default i18n;