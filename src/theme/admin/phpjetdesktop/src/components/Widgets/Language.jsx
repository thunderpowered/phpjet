import React from "react";
import {LANGUAGE_LABELS, changeLanguage} from "../../i18n";
import {useTranslation} from "react-i18next";
import './Language.scss';

export const Language = () => {
    const {t, i18n} = useTranslation();
    const onChange = lang => {
        changeLanguage(lang);
    };
    const languageJSX = [];
    for (let lang in LANGUAGE_LABELS) {
        languageJSX.push(
            <option key={lang} className={'language__select__item'} value={lang}>
                {LANGUAGE_LABELS[lang].label}
            </option>
        );
    }
    return (
        <div className={'language'} title={t('Widgets.Language.ChangeLanguage')}>
            <label htmlFor={'SelectLanguage'} className={'language__label'}>
                <img src={LANGUAGE_LABELS[i18n.language].icon} alt={LANGUAGE_LABELS[i18n.language].label} />
            </label>
            <select value={i18n.language} onChange={e => onChange(e.target.value)} className={'language__select'} id={'SelectLanguage'}>
                {languageJSX}
            </select>
        </div>
    );
}

export default Language