import React, { useState } from 'react'

const features = [
    { name: 'feature 1', start: 1, stop: 10 },
    { name: 'feature 2', start: 100, stop: 150 },
    { name: 'feature 3', start: 200, stop: 225 },
]

const FeaturesFormGroup = ({ interactor, select, children }) => {
    const [feature, setFeature] = useState('')

    const isFeatureEnabled = (feature) => {
        return interactor.start <= feature.start
            && interactor.stop >= feature.stop
    }

    const handleClick = () => {
        select(features[feature].start, features[feature].stop)
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
                            {feature.name} ({feature.start}, {feature.stop})
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
