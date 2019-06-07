import React, { useState } from 'react'

const FeaturesFormGroup = ({ features, enabled = true, select, children }) => {
    const [feature, setFeature] = useState('')

    const handleClick = () => {
        select(features[feature])
    }

    return (
        <div className="row">
            <div className="col">
                <select
                    value={feature}
                    className="form-control"
                    onChange={e => setFeature(e.target.value)}
                    disabled={features.length == 0}
                >
                    <option value="">Please select a feature</option>
                    {features.map((feature, i) => (
                        <option key={i} value={i} disabled={! feature.valid}>
                            {feature.key} - {feature.description} [{feature.valid
                                ? [feature.start, feature.stop].join(', ')
                                : 'out of selected sequence'
                            }]
                        </option>
                    ))}
                </select>
            </div>
            <div className="col-3">
                <button
                    type="button"
                    className="btn btn-block btn-info"
                    onClick={handleClick}
                    disabled={! enabled || feature == ''}
                >
                    {children}
                </button>
            </div>
        </div>
    )
}

export default FeaturesFormGroup
