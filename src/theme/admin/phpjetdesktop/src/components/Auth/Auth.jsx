import React from "react";
import Logotype from "../Widgets/Logotype";
import Version from "../Widgets/Version";
import './Auth.scss';
import FormContainer from "../Forms/FormContainer";

const Auth = ({step, action}) => {
    return (
        <div className="Auth" id="Auth">
            <div className="Auth__header p-3">
                <Logotype/>
            </div>
            <div className="Auth__body p-3">
                <FormContainer type="DefaultForm" id="AuthForm">
                    {step === 1 &&
                    <div>Step1</div>
                    }
                    {step === 2 &&
                    <div>Step2</div>
                    }
                </FormContainer>
            </div>
            <div className="Auth__footer">
                <Version/>
            </div>
        </div>
    )
};

export default Auth