import React from "react";
import Logotype from "../Widgets/Logotype";
import Version from "../Widgets/Version";
import './Auth.scss';
import FormContainer from "../Forms/FormContainer";
import {useTranslation} from "react-i18next";

const Auth = ({step, action}) => {
    const { t } = useTranslation('common');
    return (
        <div className="Auth" id="Auth">
            <div className="Auth__header p-3">
                <Logotype/>
            </div>
            <div className="Auth__body p-3">
                {step === 1 &&
                    <FormContainer id="AuthForm">
                        <input required={true} minLength={8} maxLength={64} autoComplete={'email'}
                               type={'email'} name={'email'}
                               placeholder={`${t('Auth.EnterYourEmail')}...`}/>
                        <input required={true} minLength={8} maxLength={64} autoComplete={'current-password'}
                               type={'password'} name={'password'}
                               placeholder={`${t('Auth.EnterYourPassword')}...`}/>
                        <input type={'submit'} name={'submit'} value={`${t('Auth.Login')}`} />
                    </FormContainer>
                }
                {step === 2 &&
                    <FormContainer id="AuthForm">
                        <input required={true} minLength={'6'} maxLength={'6'} autoComplete={'off'}
                               type={'password'} name={'verification'}
                               placeholder={`${t('EnterVerificationCode')}...`}/>
                        <input type={'submit'} name={'submit'} value={`${t('Auth.Verify')}`} />
                    </FormContainer>
                }
            </div>
            <div className="Auth__footer p-3">
                <Version/>
            </div>
        </div>
    )
};

export default Auth