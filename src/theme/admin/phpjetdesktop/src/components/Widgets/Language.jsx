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
            <option key={lang} className={'Language__select__item'} value={lang}>
                {LANGUAGE_LABELS[lang].label}
            </option>
        );
    }
    return (
        <div className={'Language'} title={t('Widgets.Language.ChangeLanguage')}>
            <label htmlFor={'Language'} className={'Language__label'}>
                <img src={LANGUAGE_LABELS[i18n.language].icon} alt={LANGUAGE_LABELS[i18n.language].label} />
            </label>
            <select value={i18n.language} onChange={e => onChange(e.target.value)} className={'Language__select'} id={'Language'}>
                {languageJSX}
            </select>
        </div>
    );
}

export default Language