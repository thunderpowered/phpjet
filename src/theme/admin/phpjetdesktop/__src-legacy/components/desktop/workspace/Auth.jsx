import React, {Component} from 'react';
import {FixedPopup} from "../elements/popups/FixedPopup";
import {Logotype} from "../elements/widgets/Logotype";
import {EngineVersion} from "../elements/widgets/EngineVersion";
import {BasicForm} from "../elements/forms/BasicForm";

export class Auth extends Component {
    constructor(props) {
        super(props);
        this.authActionStep1 = globalSystemRootURL + '/auth';
        this.authActionStep2 = globalSystemRootURL + '/auth/verify';
        this.state = {
            step: 1
        };
    }

    successfulAuthorization(data) {
        globalSystemActions = data.urls;
        setTimeout(() => {
            if (typeof this.props.callback !== 'undefined') {
                this.props.callback(true);
            }
        }, 1000);
    }

    secondFactor(data) {
        this.setState(() => ({step: 2}));
    }

    render() {
        return <FixedPopup>
            <div id={'Auth'}>
                <div className={'Desktop__Workspace--Auth-header p-3'}>
                    <Logotype/>
                </div>
                <div className={'Desktop__Workspace--Auth-body p-3'}>
                    {this.state.step === 1 &&
                    <BasicForm action={this.authActionStep1} actions={{
                        '2F': this.secondFactor.bind(this),
                        'S': this.successfulAuthorization.bind(this)
                    }}>
                        <input required={true} minLength={'8'} maxLength={'60'} autoComplete={'email'}
                               className={'theme__border theme__border-color'}
                               type={'email'} name={'email'}
                               placeholder={'Your Email...'}/>
                        <input required={true} minLength={'8'} maxLength={'60'} autoComplete={'current-password'}
                               className={'theme__border theme__border-color'} type={'password'} name={'password'}
                               placeholder={'Your Password...'}/>
                        <input className={'theme__background-color theme__background-color--hover theme__border--none'}
                               type={'submit'} name={'submit'} value={'Login'}/>
                    </BasicForm>
                    }
                    {this.state.step === 2 &&
                    <BasicForm action={this.authActionStep2} actions={{'S': this.successfulAuthorization.bind(this)}}>
                        <input required={true} minLength={'6'} maxLength={'6'} autoComplete={'off'}
                               className={'theme__border theme__border-color'}
                               type={'password'} name={'verification'}
                               placeholder={'Enter verification code here...'}/>
                        <input className={'theme__background-color theme__background-color--hover theme__border--none'}
                               type={'submit'} name={'submit'} value={'Verify'}/>
                    </BasicForm>
                    }
                </div>
                <div className={'Desktop__Workspace--Auth-footer p-3'}>
                    <EngineVersion/>
                </div>
            </div>
        </FixedPopup>
    }
}