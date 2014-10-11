<div class="white">
	<div class="col-md-12  container-fluid" >
		<div class="panel panel-primary">
			<div class="panel-heading">Withdraw without MultiSigX</div>
			<div class="panel-body">
				<p>After signin, you can create a MulitSigX wallet address for
						<ol>
							<li>Self</li>
							<li>Partner</li>
							<li>Friend</li>
							<li>Family</li>
							<li>Employer</li>
							<li>Employee</li>
							<li>Business</li>
							<li>Escrow</li>
							<li>Print / Cold storage</li>
							<li>MultiSigX – self escrow</li>
						</ol>
						<h3>Procedure to withdraw coins from MultiSigX address when the site is down:</h3>

						<h4>Assumptions:</h4>

							<ul>
								<li>You have created your multi sig address through MultiSigX.com and assigned to emails of friends and business associates.</li>
								<li>You have access to the PDFs sent to the emails</li>
								<li>You have deposited some coins to the MultiSigX.com</li>
							</ul>
						<h4>Procedure:</h4>

							<ul>
								<li><strong>Create withdraw request:</strong>
        <p>Find out unspent balance in the multisigx address using</p> 
								<p>http://blockchain.info/address/33LqMWWYACcooPqp6SbcA24i2i6VrnBEdD/ or http://blockchain.info/address/33LqMWWYACcooPqp6SbcA24i2i6VrnBEdD/?format=json which will show all unspent coins for the address.</p>
								</li>
								
								<li><strong>We need to find </strong>
									<p>'txid'</p>
									<p>'vout'</p>
									<p>'scriptPubKey'</p>
									<p>from the above and create a JSON script including the 'redeemScript' from the PDF file, which looks similar to this script.</p>
								</li>

<code>{
'txid':'121f1bfbe004c72776db79487873b3d90897be71',
'vout':0,
'scriptPubKey': '483045022100e09c78bac5f032053ec4f5e7c4f61473ed515fbf741b5c51ce619864e197eb05022
079ef6a552480a59bcb1f6fe42dd5ea5c6ae666881ec8d35f2f2414aca4f17409012102ed
d8022d2a5ac3066214c1089f50f264416506abdc316cbedb63df8599f6db1a',
'redeemScript':'534104f8 c0c4cc0b2c16022c911d86faf08608282e216246a39125e3a853296aee
4b4cd65f3b249304d9188 28e8055dece2199092eb6f23cd4b7405dd77444a2ed33364104ab015e
95070c6b2878c4a525e88 7674e865b723730856674336faf26e665b2cb34cddfc7e2fb42295deed
7262a947a13a50e076d202 bee98383ebc6b25907f634104759b1e901445696b2cdf8a2d20e86e5
8a560b895dc02c8dc10d23f 04a285213201cb1d765960f1b026b7d8dc7728d53d2335e9f467a24475200e5def8719180653ae'
}</code>

    <li><strong>Create withdraw address JSON:</strong>
        <p><code>{'1GpZyiAsqg1ZTH5HGrSzui4tkaGdrztJbp':500000}</code></p>
        <p>Note: The difference of the unspent balance and the amount will go to the miners for validation as txfees.</p>


<p>Open you bitcoin client like bitcoin-qt and execute the command from your console to create a raw transaction. This will not sent any coins but just create the transaction and output a value which can be used to sign the transaction.</p>

<code  class="wrapword">createrawtransaction ({
'txid':'121f1bfbe004c72776db79487873b3d90897be71',
'vout':0,
'scriptPubKey': '483045022100e09c78bac5f032053ec4f5e7c4f61473ed515fbf741b5c51ce619864e197eb05022079ef6a552480a59bcb1f6fe42dd5ea5c6ae666881ec8d35f2f2414aca4f17409012102edd8022d2a5ac3066214c1089f50f264416506abdc316cbedb63df8599f6db1a',
'redeemScript':'534104f8c0c4cc0b2c16022c911d86faf08608282e216246a39125e3a853296aee4b4cd65f3b249304d918828e8055dece2199092eb6f23cd4b7405dd77444a2ed33364104ab015e95070c6b2878c4a525e887674e865b723730856674336faf26e665b2cb34cddfc7e2fb42295deed7262a947a13a50e076d202bee98383ebc6b25907f634104759b1e901445696b2cdf8a2d20e86e58a560b895dc02c8dc10d23f04a285213201cb1d765960f1b026b7d8dc7728d53d2335e9f467a24475200e5def8719180653ae'
}, {'1GpZyiAsqg1ZTH5HGrSzui4tkaGdrztJbp':500000})</code>
<br><br>
<p>Output:
<code  class="wrapword">"010000000169d7cfb615fd2f9b77f1fb872f2129888fd8201beeb9655661bfd5ed156de52a0000000000ffffffff05905f0100000000001976a914fa76d9d51044aa424b3e7d9777fa4cc50f6bd26b88ac606b042a0100000017a9141239cd22ca4e1ae69a965eb590a7aa8caa28566587005ed0b2000000001976a914fcd7e4abd320c950a26a778cf60d4a7f6384fd4888ac002f6859000000001976a914dd9a66bcd9107a1bc2e9f40486b71bf97904b38988ac0065cd1d000000001976a914d0e3649723d71939fbea7ad38112a7ff3672954b88ac00000000"</code></p>
</li>
<li><strong>signrawtransaction:</strong>
<p>Using the above output from createrawtransaction to sign these transaction for 2 of 3 or 3 of 3 as per security defined while creating the transaction.</p>

<p>signrawtransaction ({'output of createrawtransaction'},{'array of original transaction'},{'private key of first signing authority'})</p>

<code  class="wrapword">signrawtransaction({'010000000169d7cfb615fd2f9b77f1fb872f2129888fd8201beeb9655661bfd5ed156de52a0000000000ffffffff05905f0100000000001976a914fa76d9d51044aa424b3e7d9777fa4cc50f6bd26b88ac606b042a0100000017a9141239cd22ca4e1ae69a965eb590a7aa8caa28566587005ed0b2000000001976a914fcd7e4abd320c950a26a778cf60d4a7f6384fd4888ac002f6859000000001976a914dd9a66bcd9107a1bc2e9f40486b71bf97904b38988ac0065cd1d000000001976a914d0e3649723d71939fbea7ad38112a7ff3672954b88ac00000000'},
{
'txid':'121f1bfbe004c72776db79487873b3d90897be71',
'vout':0,
'scriptPubKey': '483045022100e09c78bac5f032053ec4f5e7c4f61473ed515fbf741b5c51ce619864e197eb05022079ef6a552480a59bcb1f6fe42dd5ea5c6ae666881ec8d35f2f2414aca4f17409012102edd8022d2a5ac3066214c1089f50f264416506abdc316cbedb63df8599f6db1a',

'redeemScript':'534104f8 c0c4cc0b2c16022c911d86faf08608282e216246a39125e3a853296aee4b4cd65f3b249304d9188 28e8055dece2199092eb6f23cd4b7405dd77444a2ed33364104ab015e95070c6b2878c4a525e887674e865b723730856674336faf26e665b2cb34cddfc7e2fb42295deed7262a947a13a50e076d202bee98383ebc6b25907f634104759b1e901445696b2cdf8a2d20e86e58a560b895dc02c8dc10d23f 04a285213201cb1d765960f1b026b7d8dc7728d53d2335e9f467a24475200e5def8719180653ae'},

{'5HyBeyrMRUyXo4KxQhQA9CPfAhPBLtcn41VEihCVYFJxUYDwAoE'}
)
</code>
<p>This will give an output of the raw transaction with a validation code true/false.</p>

<code  class="wrapword">{ "hex" : "010000000169d7cfb615fd2f9b77f1fb872f2129888fd8201beeb9655661bfd5ed156de52a00000000fd1401004730440220412f36bb1d9b7dd16e9763c10e29aa48495784f7581d0e621115044ca7e819bd02201e20c08dfca4b3393d8c989d0fc3a7cd026efa7720a24e724b29492f18ff82b2014cc9524104d7a59cad89998b63ba70806b29860526af3de22ac8bf83a53d551845fb6dd295545a2ca7b19280e89154843c4d0c2d4dc07456ee44f11f1a7904daeee6ae4a3f41046e182f86cd5f7f8d07464c77a5446fa7e77e51a1afda6d82a43dbb6ab11eabc734be18a976c070fa721bfda8f63b6ff3449149f0ff793889069517ccd209671c4104c070a71c022081c6706cf3506cfdee243000b24bac2cb2c56b8bd37c515a8962ac1b078dd84090e2bc134c3ee5d8bc445b8aa22851067ed89ea60dcf1b507f0d53aeffffffff05905f0100000000001976a914fa76d9d51044aa424b3e7d9777fa4cc50f6bd26b88ac606b042a0100000017a9141239cd22ca4e1ae69a965eb590a7aa8caa28566587005ed0b2000000001976a914fcd7e4abd320c950a26a778cf60d4a7f6384fd4888ac002f6859000000001976a914dd9a66bcd9107a1bc2e9f40486b71bf97904b38988ac0065cd1d000000001976a914d0e3649723d71939fbea7ad38112a7ff3672954b88ac00000000" , "complete" : false}</code>
</li>
<li><strong>signrawtransaction again:</strong>
<p>Similarly using the 2nd private key, you complete the signrawtransaction again, which will again with you a validation code of true/false.</p>

<p>If you have 2 of 3 security, then when you sign the 2nd time you will get "complete":true</p>

<code  class="wrapword">{ "hex" : "010000000169d7cfb615fd2f9b77f1fb872f2129888fd8201beeb9655661bfd5ed156de52a00000000fd5e01004730440220412f36bb1d9b7dd16e9763c10e29aa48495784f7581d0e621115044ca7e819bd02201e20c08dfca4b3393d8c989d0fc3a7cd026efa7720a24e724b29492f18ff82b201493046022100d2f823dd0e377845403703af4c858fbef0f43c3942ae394c05f87f7de01e8a89022100e40ab1f247eee5bbe6c88f07327c0757a7d1e0e93074a8c34af9f65a908b4d2c014cc9524104d7a59cad89998b63ba70806b29860526af3de22ac8bf83a53d551845fb6dd295545a2ca7b19280e89154843c4d0c2d4dc07456ee44f11f1a7904daeee6ae4a3f41046e182f86cd5f7f8d07464c77a5446fa7e77e51a1afda6d82a43dbb6ab11eabc734be18a976c070fa721bfda8f63b6ff3449149f0ff793889069517ccd209671c4104c070a71c022081c6706cf3506cfdee243000b24bac2cb2c56b8bd37c515a8962ac1b078dd84090e2bc134c3ee5d8bc445b8aa22851067ed89ea60dcf1b507f0d53aeffffffff05905f0100000000001976a914fa76d9d51044aa424b3e7d9777fa4cc50f6bd26b88ac606b042a0100000017a9141239cd22ca4e1ae69a965eb590a7aa8caa28566587005ed0b2000000001976a914fcd7e4abd320c950a26a778cf60d4a7f6384fd4888ac002f6859000000001976a914dd9a66bcd9107a1bc2e9f40486b71bf97904b38988ac0065cd1d000000001976a914d0e3649723d71939fbea7ad38112a7ff3672954b88ac00000000", "complete" : true}</code>
<p>
When we have "complete": true, you can submit the "hex" with sendrawtransaction to the client to complete the transaction.</p>
</li>
<li><strong>sendrawtransaction:</strong>
<code  class="wrapword">sendrawtransaction('010000000169d7cfb615fd2f9b77f1fb872f2129888fd8201beeb9655661bfd5ed156de52a00000000fd5e01004730440220412f36bb1d9b7dd16e9763c10e29aa48495784f7581d0e621115044ca7e819bd02201e20c08dfca4b3393d8c989d0fc3a7cd026efa7720a24e724b29492f18ff82b201493046022100d2f823dd0e377845403703af4c858fbef0f43c3942ae394c05f87f7de01e8a89022100e40ab1f247eee5bbe6c88f07327c0757a7d1e0e93074a8c34af9f65a908b4d2c014cc9524104d7a59cad89998b63ba70806b29860526af3de22ac8bf83a53d551845fb6dd295545a2ca7b19280e89154843c4d0c2d4dc07456ee44f11f1a7904daeee6ae4a3f41046e182f86cd5f7f8d07464c77a5446fa7e77e51a1afda6d82a43dbb6ab11eabc734be18a976c070fa721bfda8f63b6ff3449149f0ff793889069517ccd209671c4104c070a71c022081c6706cf3506cfdee243000b24bac2cb2c56b8bd37c515a8962ac1b078dd84090e2bc134c3ee5d8bc445b8aa22851067ed89ea60dcf1b507f0d53aeffffffff05905f0100000000001976a914fa76d9d51044aa424b3e7d9777fa4cc50f6bd26b88ac606b042a0100000017a9141239cd22ca4e1ae69a965eb590a7aa8caa28566587005ed0b2000000001976a914fcd7e4abd320c950a26a778cf60d4a7f6384fd4888ac002f6859000000001976a914dd9a66bcd9107a1bc2e9f40486b71bf97904b38988ac0065cd1d000000001976a914d0e3649723d71939fbea7ad38112a7ff3672954b88ac00000000')</code>

<p>This will send the transaction created by createrawtransaction to the blockchain and complete it....</p>
</li>
<p>In summary, one has to
		<ul>
    <li>createrawtransaction
						<ul>
        <li>requires the transaction which has unspent coins</li>
        <li>requires new addresses where to send coinds</li>
        <li>residual is sent as txfees</li>
						</ul>
					</li>
    <li>signrawtransaction (n number of times as defined in creating the multisigx wallet)
        <ul>
								<li>requires the transaction which has unspent coins</li>
        <li>requires the private key of individual signers</li>
								</ul>
					</li>
    <li>sendrawtransaction
        <ul>
								<li>if signrawtransaction complete is "true", then only sendrawtransaction is accepted in the blockchain and funds are transferred.</li>
								</ul>
				</li>
			</ul>
</p>


				</p>
								
			</div>
		</div>
	</div>
</div>