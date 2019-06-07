import React, { useState } from 'react'

import CoordinateField from './CoordinateField'
import ChainsFormGroup from './ChainsFormGroup'
import ExtractFormGroup from './ExtractFormGroup'
import MatureProteinList from './MatureProteinList'
import SubsequenceFormGroup from './SubsequenceFormGroup'

const SequenceEditor = ({ source, protein, update }) => {
    const [name, setName] = useState(protein.name)
    const [start, setStart] = useState(protein.start)
    const [stop, setStop] = useState(protein.stop)

    const setCoordinates = (start, stop) => {
        setStart(start)
        setStop(stop)
    }

    const isNameSet = name.trim() != ''

    const areCoordinatesSet = start != '' && stop != ''

    const doesNameExist = source.matures.filter(m => {
        return m.name == name.trim()
    }).length > 0

    const doCoordinatesExist = source.matures.filter(m => {
        return m.start == start && m.stop == stop
    }).length > 0

    const doesMatureExist = source.matures.filter(m => {
        return m.name == name.trim() && m.start == start && m.stop == stop
    }).length > 0

    const isNameValid = ! isNameSet || ! doesNameExist || doesMatureExist

    const areCoordinatesValid = ! areCoordinatesSet || ! doCoordinatesExist || doesMatureExist

    const isMatureValid = isNameSet && areCoordinatesSet
        && (doesMatureExist || (! doesNameExist && ! doCoordinatesExist))

    const handleValidate = () => {
        update({name: name.trim(), start: start, stop: stop})
    }

    const handleReset = () => {
        setCoordinates(1, source.sequence.length)
    }

    return (
        <React.Fragment>
            {source.matures.length == 0 ? (
                <p>
                    No sequence defined on this uniprot entry yet.
                </p>
            ) : (
                <React.Fragment>
                    <p>
                        Existing sequences on this uniprot entry:
                    </p>
                    <MatureProteinList matures={source.matures} select={update} />
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
                        max={source.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Start
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <CoordinateField
                        value={stop}
                        set={setStop}
                        max={source.sequence.length}
                        valid={areCoordinatesValid}
                    >
                        Stop
                    </CoordinateField>
                </div>
                <div className="col-3">
                    <button
                        type="button"
                        className="btn btn-block btn-primary"
                        onClick={handleValidate}
                        disabled={! isMatureValid}
                    >
                        Validate
                    </button>
                </div>
            </div>
            {source.chains.length == 0 ? null : (
                <ChainsFormGroup chains={source.chains} set={setCoordinates}>
                    Extract coordinates
                </ChainsFormGroup>
            )}
            <SubsequenceFormGroup sequence={source.sequence} set={setCoordinates}>
                Extract coordinates
            </SubsequenceFormGroup>
            <ExtractFormGroup sequence={source.sequence} set={setCoordinates}>
                Extract coordinates
            </ExtractFormGroup>
            <div className="row">
                <div className="col offset-9">
                    <button
                        type="button"
                        className="btn btn-block btn-info"
                        onClick={handleReset}
                    >
                        Set to full length
                    </button>
                </div>
            </div>
        </React.Fragment>
    )
}

export default SequenceEditor;
