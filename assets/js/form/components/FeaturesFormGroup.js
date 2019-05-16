import React, { useState } from 'react'

const FeaturesFormGroup = ({ interactor, set, children }) => {
    const features = interactor.protein.features

    const [feature, setFeature] = useState('')

    const isFeatureEnabled = (feature) => {
        return interactor.start <= feature.start
            && interactor.stop >= feature.stop
    }

    const handleClick = () => {
        set(features[feature])
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={feature}
                    className="form-control"
                    onChange={e => setFeature(e.target.value)}
                >
                    <option value="">Please select a feature</option>
                    {features.map((feature, index) => (
                        <option key={index} value={index} disabled={! isFeatureEnabled(feature)}>
                            {feature.key} - {feature.description} ({feature.start}, {feature.stop})
                        </option>
                    ))}
                </select>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={handleClick}
                    disabled={feature == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default FeaturesFormGroup
