import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ChainsFormGroup from './ChainsFormGroup'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const SequenceEditor = ({ current, sequence, matures, chains, update }) => {
    const [name, setName] = useState(current.name)
    const [start, setStart] = useState(current.start)
    const [stop, setStop] = useState(current.stop)

    const isNameSet = name.trim() != ''

    const isNameWellFormatted = name.trim().match(/^[^\s]+$/)

    const areCoordinatesSet = start != '' && stop != ''

    const doesNameExist = matures.filter(m => {
        return m.name == name.trim()
    }).length > 0

    const doCoordinatesExist = matures.filter(m => {
        return m.start == start && m.stop == stop
    }).length > 0

    const doesMatureExist = matures.filter(m => {
        return m.name == name.trim() && m.start == start && m.stop == stop
    }).length > 0

    const isNameValid = !isNameSet || (isNameWellFormatted && !doesNameExist) || doesMatureExist

    const areCoordinatesValid = !areCoordinatesSet || !doCoordinatesExist || doesMatureExist

    const isMatureValid = isNameSet && isNameWellFormatted && areCoordinatesSet
        && (doesMatureExist || (!doesNameExist && !doCoordinatesExist))

    const setCoordinates = (start, stop) => {
        setStart(start)
        setStop(stop)
    }

    const submit = () => {
        update({ name: name.trim(), start: start, stop: stop })
    }

    const reset = () => {
        setCoordinates(1, sequence.length)
    }

    return (
        <React.Fragment>
            {matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                    <React.Fragment>
                        <p>
                            Existing sequences on this uniprot entry:
                    </p>
                        <MatureProteinList matures={matures} select={update} />
                    </React.Fragment>
                )}
            <div className="row">
                <div className="col-3">
                    <input
                        type="text"
                        className={'form-control' + (isNameValid ? '' : ' is-invalid')}
                        placeholder="Name"
                        value={name}
                        onChange={e => setName(e.target.value)}
                    />
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={start}
                        set={setStart}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={stop}
                        set={setStop}
                        max={sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={e => submit()}
                        disabled={!isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            {chains.length == 0 ? null : (
                <ChainsFormGroup chains={chains} set={setCoordinates}>
                    Extract coordinates
                </ChainsFormGroup>
            )}
            <SubsequenceFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={e => reset()}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default SequenceEditor;
