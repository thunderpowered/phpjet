import React from "react";
import Logotype from "../Widgets/Logotype";
import Version from "../Widgets/Version";
import './Auth.scss';
import FormContainer from "../Forms/FormContainer";
import {useTranslation} from "react-i18next";

const Auth = ({action, onSubmit}) => {
    const {t} = useTranslation('common');
    return (
        <div className="Auth" id="Auth">
            <div className="Auth__header pt-5 pb-4">
                <Logotype/>
            </div>
            <div className="Auth__body p-5 pt-0 pb-0">
                {action === '1F' &&
                    <FormContainer id="AuthForm" onSubmit={onSubmit}>
                        <input required={true} minLength={8} maxLength={64} autoComplete={'email'}
                               type={'email'} name={'email'}
                               placeholder={`${t('Auth.EnterYourEmail')}...`}/>
                        <input required={true} minLength={8} maxLength={64} autoComplete={'current-password'}
                               type={'password'} name={'password'}
                               placeholder={`${t('Auth.EnterYourPassword')}...`}/>
                        <input type={'submit'} name={'submit'} value={`${t('Auth.Login')}`} />
                    </FormContainer>
                }
                {action === '2F' &&
                    <FormContainer id="AuthForm" onSubmit={onSubmit}>
                        <input required={true} minLength={'6'} maxLength={'6'} autoComplete={'off'}
                               type={'password'} name={'verification'}
                               placeholder={`${t('Auth.EnterVerificationCode')}...`}/>
                        <input type={'submit'} name={'submit'} value={`${t('Auth.Verify')}`} />
                    </FormContainer>
                }
            </div>
            <div className="Auth__footer pb-3 pt-2">
                <Version/>
            </div>
        </div>
    )
};

export default Auth