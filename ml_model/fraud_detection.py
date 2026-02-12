import numpy as np
from sklearn.ensemble import IsolationForest
from flask import Flask, request, jsonify

# Initialize Flask app
app = Flask(__name__)

# Placeholder for Isolation Forest model
model = IsolationForest(n_estimators=100, contamination=0.1, random_state=42)

# Example training data (replace with real data)
X_train = np.random.rand(100, 3)  # 100 samples, 3 features
model.fit(X_train)

@app.route('/predict', methods=['POST'])
def predict():
    data = request.get_json()
    try:
        # Extract features from request
        features = np.array([
            [
                data['amount'],
                data['transactions_last_hour'],
                data['distance_from_last_location']
            ]
        ])

        # Predict using the model
        score = model.decision_function(features)[0]
        is_fraud = model.predict(features)[0] == -1

        return jsonify({
            'fraud_score': score,
            'is_fraud': is_fraud
        })
    except KeyError as e:
        return jsonify({'error': f'Missing key: {str(e)}'}), 400

if __name__ == '__main__':
    app.run(debug=True)